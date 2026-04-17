<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\BuffetPackage;
use App\Models\BuffetSale;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Services\AccountingService;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BuffetController extends Controller
{
    public function packages(): View
    {
        $packages = BuffetPackage::orderBy('name')->paginate(20);

        return view('restaurant.buffet.packages', compact('packages'));
    }

    public function storePackage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'adult_price' => 'required|numeric|min:0.01',
            'child_price' => 'nullable|numeric|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        BuffetPackage::create([
            'name' => $data['name'],
            'adult_price' => $data['adult_price'],
            'child_price' => $data['child_price'] ?? 0,
            'available_days' => $data['available_days'] ?? [],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', __('general.restaurant.buffet.messages.package_created'));
    }

    public function updatePackage(Request $request, BuffetPackage $buffetPackage): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'adult_price' => 'required|numeric|min:0.01',
            'child_price' => 'nullable|numeric|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $buffetPackage->update([
            'name' => $data['name'],
            'adult_price' => $data['adult_price'],
            'child_price' => $data['child_price'] ?? 0,
            'available_days' => $data['available_days'] ?? [],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return back()->with('success', __('general.restaurant.buffet.messages.package_updated'));
    }

    public function deactivatePackage(BuffetPackage $buffetPackage): RedirectResponse
    {
        $buffetPackage->update(['is_active' => false]);

        return back()->with('success', __('general.restaurant.buffet.messages.package_deactivated'));
    }

    public function index(Request $request): View
    {
        $sales = BuffetSale::with(['package', 'booking', 'server', 'settler'])
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->string('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->string('to')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20);

        return view('restaurant.buffet.index', compact('sales'));
    }

    public function create(): View
    {
        $packages = BuffetPackage::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::active()->with('guest', 'room')->latest()->limit(100)->get();

        return view('restaurant.buffet.create', compact('packages', 'bookings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'buffet_package_id' => 'required|uuid|exists:buffet_packages,id',
            'sale_type' => 'required|in:walkin,booking',
            'booking_id' => 'required_if:sale_type,booking|nullable|uuid|exists:bookings,id',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $package = BuffetPackage::findOrFail($data['buffet_package_id']);
        $children = (int) ($data['children_count'] ?? 0);

        if (!$this->isPackageAvailableNow($package)) {
            return back()->withErrors([
                'buffet_package_id' => __('general.restaurant.buffet.messages.package_unavailable_now'),
            ])->withInput();
        }

        $total = ((int) $data['adults_count'] * (float) $package->adult_price)
            + ($children * (float) $package->child_price);

        if ($total <= 0) {
            return back()->withErrors([
                'adults_count' => __('general.restaurant.buffet.messages.invalid_total'),
            ])->withInput();
        }

        $sale = DB::transaction(function () use ($data, $package, $children, $total) {
            return BuffetSale::create([
                'buffet_package_id' => $package->id,
                'booking_id' => $data['sale_type'] === 'booking' ? $data['booking_id'] : null,
                'sale_type' => $data['sale_type'],
                'adults_count' => (int) $data['adults_count'],
                'children_count' => $children,
                'package_name_snapshot' => $package->name,
                'adult_price_snapshot' => $package->adult_price,
                'child_price_snapshot' => $package->child_price,
                'total_amount' => $total,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'served_by' => auth()->id(),
            ]);
        });

        return redirect()->route('restaurant.buffet.show', $sale)
            ->with('success', __('general.restaurant.buffet.messages.sale_created'));
    }

    public function show(BuffetSale $buffetSale): View
    {
        $buffetSale->load(['package', 'booking.guest', 'server', 'settler']);

        return view('restaurant.buffet.show', compact('buffetSale'));
    }

    public function chargeToBooking(BuffetSale $buffetSale): RedirectResponse
    {
        if ($buffetSale->sale_type !== 'booking' || !$buffetSale->booking_id) {
            return back()->withErrors(['sale_type' => __('general.restaurant.buffet.messages.booking_required')]);
        }

        if ($buffetSale->status !== 'pending') {
            return back()->withErrors(['status' => __('general.restaurant.buffet.messages.invalid_status_action')]);
        }

        DB::transaction(function () use ($buffetSale) {
            $exchangeRate = (float) (DB::table('system_settings')
                ->where('key', 'tzs_exchange_rate')
                ->value('value') ?? 2500);
            $amountUsd = round(((float) $buffetSale->total_amount) / $exchangeRate, 2);

            BookingCharge::updateOrCreate(
                [
                    'booking_id' => $buffetSale->booking_id,
                    'source' => 'restaurant',
                    'reference_id' => $buffetSale->id,
                ],
                [
                    'charge_type' => 'restaurant',
                    'order_id' => null,
                    'description' => "Buffet {$buffetSale->package_name_snapshot} ({$buffetSale->adults_count}A/{$buffetSale->children_count}C)",
                    'amount' => $amountUsd,
                    'currency' => 'USD',
                    'amount_tzs' => $buffetSale->total_amount,
                    'status' => 'unpaid',
                    'created_by' => auth()->id(),
                ]
            );

            $buffetSale->update(['status' => 'charged']);
        });

        return redirect()->route('finance.checkout.show', $buffetSale->booking_id)
            ->with('success', __('general.restaurant.buffet.messages.charged_to_folio'));
    }

    public function settleWalkin(Request $request, BuffetSale $buffetSale): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,mobile',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        if ($buffetSale->sale_type !== 'walkin') {
            return back()->withErrors(['sale_type' => __('general.restaurant.buffet.messages.walkin_only')]);
        }

        if ($buffetSale->status !== 'pending') {
            return back()->withErrors(['status' => __('general.restaurant.buffet.messages.invalid_status_action')]);
        }

        DB::transaction(function () use ($request, $buffetSale) {
            $method = $request->string('payment_method')->toString();
            $exchangeRate = (float) (DB::table('system_settings')
                ->where('key', 'tzs_exchange_rate')
                ->value('value') ?? 2500);
            $amountUsd = FinancePayment::toUsd((float) $buffetSale->total_amount, 'TZS', $exchangeRate);
            $normalizedMethod = $method === 'mobile' ? 'mobile_money' : $method;
            $paymentReference = $request->string('payment_reference')->toString() ?: null;

            $payment = FinancePayment::create([
                'payment_type' => 'walkin',
                'checkout_id' => null,
                'order_id' => null,
                'booking_id' => null,
                'currency' => 'TZS',
                'amount' => $buffetSale->total_amount,
                'amount_usd' => $amountUsd,
                'exchange_rate' => $exchangeRate,
                'method' => $normalizedMethod,
                'status' => 'completed',
                'reference' => $paymentReference,
                'notes' => "Buffet walk-in {$buffetSale->sale_number}",
                'created_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            FinancialTransaction::record([
                'type' => 'walkin_sale',
                'source_module' => 'restaurant',
                'payment_id' => $payment->id,
                'booking_id' => null,
                'order_id' => null,
                'currency' => 'TZS',
                'amount' => $buffetSale->total_amount,
                'amount_usd' => $amountUsd,
                'exchange_rate' => $exchangeRate,
                'payment_method' => $payment->method,
                'description' => "Buffet walk-in payment {$buffetSale->sale_number}",
            ], (string) auth()->id());

            app(AccountingService::class)->postRestaurantSettlement(
                orderNo: $buffetSale->sale_number,
                orderId: $buffetSale->id,
                amount: (float) $buffetSale->total_amount,
                paymentMethod: $payment->method,
                actorId: (string) auth()->id()
            );

            $buffetSale->update([
                'status' => 'settled',
                'payment_method' => $normalizedMethod,
                'payment_reference' => $paymentReference,
                'settled_by' => auth()->id(),
                'settled_at' => now(),
            ]);

            app(ReceiptService::class)->getOrCreateReceipt($buffetSale->fresh());
        });

        return redirect()->route('restaurant.buffet.show', $buffetSale)
            ->with('success', __('general.restaurant.buffet.messages.walkin_settled'));
    }

    private function isPackageAvailableNow(BuffetPackage $package): bool
    {
        if (!$package->is_active) {
            return false;
        }

        $today = strtolower(now()->format('l'));
        $allowedDays = collect($package->available_days ?? [])->map(fn($d) => strtolower((string) $d));
        if ($allowedDays->isNotEmpty() && !$allowedDays->contains($today)) {
            return false;
        }

        $now = now()->format('H:i:s');
        if ($package->start_time && $now < $package->start_time) {
            return false;
        }
        if ($package->end_time && $now > $package->end_time) {
            return false;
        }

        return true;
    }
}

