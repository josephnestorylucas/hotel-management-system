<?php

namespace App\Http\Controllers;

use App\Models\BuffetPackage;
use App\Models\BuffetSale;
use App\Models\BookingCharge;
use App\Helpers\CurrencyHelper;
use App\Services\ReceiptService;
use App\Services\AccountingService;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BuffetPosController extends Controller
{
    public function index(): View
    {
        $now = now()->format('H:i:s');
        $day = strtolower(now()->format('l'));

        $sessions = BuffetPackage::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                  ->orWhere(function ($inner) use ($now) {
                      $inner->where('start_time', '<=', $now)
                            ->where('end_time', '>=', $now);
                  });
            })
            ->orderBy('name')
            ->get()
            ->filter(function ($session) use ($day) {
                if (empty($session->available_days)) {
                    return true;
                }
                $days = array_map('strtolower', (array) $session->available_days);
                return in_array($day, $days);
            });

        return view('buffet.pos', compact('sessions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'buffet_package_id' => 'required|uuid|exists:buffet_packages,id',
            'adults'            => 'required|integer|min:0',
            'children'          => 'required|integer|min:0',
            'customer_name'     => 'nullable|string|max:150',
            'notes'             => 'nullable|string',
            'payment_method'    => 'required|in:cash,mobile,card,charge_to_booking',
            'booking_id'        => 'required_if:payment_method,charge_to_booking|nullable|uuid|exists:bookings,id',
        ]);

        $adults = (int) ($data['adults'] ?? 0);
        $children = (int) ($data['children'] ?? 0);

        abort_if($adults === 0 && $children === 0, 422, 'At least one person required.');

        $session = BuffetPackage::findOrFail($data['buffet_package_id']);
        $isChargeToBooking = $data['payment_method'] === 'charge_to_booking';

        $total = ($adults * (float) $session->adult_price)
            + ($children * (float) $session->child_price);

        $sale = DB::transaction(function () use ($data, $session, $adults, $children, $total, $isChargeToBooking) {
            $saleType = $isChargeToBooking ? 'booking' : 'walkin';

            $sale = BuffetSale::create([
                'buffet_package_id'    => $session->id,
                'booking_id'           => $isChargeToBooking ? ($data['booking_id'] ?? null) : null,
                'sale_type'            => $saleType,
                'adults_count'         => $adults,
                'children_count'       => $children,
                'package_name_snapshot' => $session->name,
                'adult_price_snapshot'  => $session->adult_price,
                'child_price_snapshot'  => $session->child_price,
                'total_amount'          => $total,
                'status'                => $isChargeToBooking ? 'charged' : 'pending',
                'notes'                 => $data['notes'] ?? null,
                'served_by'             => (string) Auth::id(),
            ]);

            if ($isChargeToBooking) {
                $exchangeRate = CurrencyHelper::getExchangeRate();
                $amountUsd = round($total / $exchangeRate, 2);

                BookingCharge::updateOrCreate(
                    [
                        'booking_id' => $data['booking_id'],
                        'source' => 'restaurant',
                        'reference_id' => $sale->id,
                    ],
                    [
                        'charge_type' => 'restaurant',
                        'order_id' => null,
                        'description' => "Buffet {$session->name} ({$adults}A/{$children}C)",
                        'amount' => $amountUsd,
                        'currency' => 'USD',
                        'amount_tzs' => $total,
                        'status' => 'unpaid',
                        'created_by' => (string) Auth::id(),
                    ]
                );
            } else {
                $paymentMethod = $data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method'];

                $sale->update([
                    'status'           => 'settled',
                    'payment_method'   => $paymentMethod,
                    'settled_by'       => (string) Auth::id(),
                    'settled_at'       => now(),
                ]);

                $exchangeRate = CurrencyHelper::getExchangeRate();
                $amountUsd = FinancePayment::toUsd($total, 'TZS', $exchangeRate);

                FinancePayment::create([
                    'payment_type'  => 'walkin',
                    'checkout_id'   => null,
                    'order_id'      => null,
                    'method'        => $paymentMethod,
                    'amount'        => $total,
                    'currency'      => 'TZS',
                    'amount_usd'    => $amountUsd,
                    'exchange_rate' => $exchangeRate,
                    'status'        => 'completed',
                    'created_by'    => (string) Auth::id(),
                    'paid_at'       => now(),
                ]);

                FinancialTransaction::record([
                    'type'            => 'payment',
                    'source_module'   => 'restaurant',
                    'payment_id'      => null,
                    'order_id'        => null,
                    'currency'        => 'TZS',
                    'amount'          => $total,
                    'amount_usd'      => $amountUsd,
                    'exchange_rate'   => $exchangeRate,
                    'payment_method'  => $paymentMethod,
                    'description'     => "Buffet sale {$sale->sale_number}",
                ], (string) Auth::id());
            }

            app(ReceiptService::class)->getOrCreateReceipt($sale);

            return $sale;
        });

        if ($isChargeToBooking) {
            return redirect()->route('buffet.pos.index')
                ->with('success', "Buffet sale {$sale->sale_number} charged to guest folio.");
        }

        return redirect()->route('buffet.pos.index')
            ->with('success', "Buffet sale {$sale->sale_number} completed successfully.");
    }
}
