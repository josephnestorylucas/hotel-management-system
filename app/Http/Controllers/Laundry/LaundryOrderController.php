<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\FinancePayment;
use App\Models\FinancialTransaction;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use App\Models\StoreNotification;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AccountingService;

class LaundryOrderController extends Controller
{
    // GET /laundry/orders
    public function index(Request $request): View
    {
        $orders = LaundryOrder::with(['items.serviceItem.service', 'receiver'])
            ->when($request->status,        fn ($q) => $q->where('status', $request->status))
            ->when($request->customer_type, fn ($q) => $q->where('customer_type', $request->customer_type))
            ->when($request->date,          fn ($q) => $q->whereDate('created_at', $request->date))
            ->when($request->search, fn ($q) => $q->where(function ($q2) use ($request) {
                $q2->where('order_number',    'like', '%' . $request->search . '%')
                   ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                   ->orWhere('room_number',   'like', '%' . $request->search . '%')
                   ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            }))
            ->latest()
            ->paginate(25);

        $statusCounts = LaundryOrder::selectRaw('status, count(*) as count')
            ->whereNotIn('status', ['settled', 'cancelled'])
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('laundry.orders.index', compact('orders', 'statusCounts'));
    }

    // GET /laundry/orders/create
    public function create(): View
    {
        $services = LaundryService::with(['serviceItems' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        // Get active bookings for the guest dropdown
        $bookings = Booking::with(['guest', 'room'])
            ->where('status', 'checked_in')
            ->get();

        return view('laundry.orders.create', compact('services', 'bookings'));
    }

    // POST /laundry/orders
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_type'           => 'required|in:guest,walkin',
            'booking_id'              => 'required_if:customer_type,guest|nullable|uuid',
            'room_number'             => 'nullable|string|max:20',
            'customer_name'           => 'required_if:customer_type,walkin|nullable|string|max:150',
            'customer_phone'          => 'nullable|string|max:30',
            'special_instructions'    => 'nullable|string|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.service_item_id' => 'required|uuid|exists:laundry_service_items,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.notes'           => 'nullable|string|max:255',
        ]);

        // If guest, auto-fill room_number from booking
        if ($data['customer_type'] === 'guest' && !empty($data['booking_id'])) {
            $booking = Booking::with('room')->find($data['booking_id']);
            if ($booking && $booking->room && empty($data['room_number'])) {
                $data['room_number'] = $booking->room->room_number;
            }
        }

        $order = DB::transaction(function () use ($data) {

            // Find the longest turnaround among selected services
            $maxTurnaround = 0;
            foreach ($data['items'] as $item) {
                $serviceItem = LaundryServiceItem::with('service')->findOrFail($item['service_item_id']);
                if ($serviceItem->service->turnaround_hours > $maxTurnaround) {
                    $maxTurnaround = $serviceItem->service->turnaround_hours;
                }
            }

            $order = LaundryOrder::create([
                'customer_type'       => $data['customer_type'],
                'booking_id'          => $data['booking_id'] ?? null,
                'room_number'         => $data['room_number'] ?? null,
                'customer_name'       => $data['customer_name'] ?? null,
                'customer_phone'      => $data['customer_phone'] ?? null,
                'special_instructions' => $data['special_instructions'] ?? null,
                'status'              => 'received',
                'expected_ready_at'   => now()->addHours($maxTurnaround),
                'received_by'         => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $serviceItem = LaundryServiceItem::findOrFail($item['service_item_id']);

                LaundryOrderItem::create([
                    'laundry_order_id'        => $order->id,
                    'laundry_service_item_id' => $serviceItem->id,
                    'quantity'                => $item['quantity'],
                    'unit_price'              => $serviceItem->price,
                    'subtotal'                => $serviceItem->price * $item['quantity'],
                    'notes'                   => $item['notes'] ?? null,
                ]);
            }

            $order->load('items');
            $order->recalculate();

            return $order;
        });

        // Notify SUPERVISOR and LAUNDRY_MANAGER
        User::whereHas('role', fn ($q) => $q->whereIn('name', ['supervisor', 'laundry_manager']))
            ->get()
            ->each(fn ($u) => StoreNotification::create([
                'user_id'        => $u->id,
                'type'           => 'new_laundry_order',
                'title'          => 'New Laundry Order Received',
                'body'           => "Order {$order->order_number} — " .
                                    ($order->customer_type === 'guest'
                                        ? "Room {$order->room_number}"
                                        : $order->customer_name) .
                                    " — {$order->items->count()} item(s). Ready by: " .
                                    $order->expected_ready_at->format('d M H:i'),
                'reference_type' => 'laundry_order',
                'reference_id'   => $order->id,
                'action_url'     => route('laundry.orders.show', $order->id),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('laundry.orders.show', $order)
            ->with('success', "Order {$order->order_number} created. Ready by: " .
                              $order->expected_ready_at->format('d M Y H:i'));
    }

    // GET /laundry/orders/{laundryOrder}
    public function show(LaundryOrder $laundryOrder): View
    {
        $laundryOrder->load([
            'items.serviceItem.service',
            'receiver', 'processor', 'deliverer', 'settler',
        ]);

        return view('laundry.orders.show', compact('laundryOrder'));
    }

    // POST /laundry/orders/{laundryOrder}/process
    public function process(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'received', 422, 'Only received orders can be started.');

        $laundryOrder->update([
            'status'       => 'processing',
            'processed_by' => auth()->id(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} is now processing.");
    }

    // POST /laundry/orders/{laundryOrder}/ready
    public function markReady(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'processing', 422, 'Order must be processing before marking ready.');

        $laundryOrder->update([
            'status'   => 'ready',
            'ready_at' => now(),
        ]);

        // Notify FRONT_DESK for guest orders
        if ($laundryOrder->customer_type === 'guest') {
            User::whereHas('role', fn ($q) => $q->where('name', 'front_desk'))
                ->get()
                ->each(fn ($u) => StoreNotification::create([
                    'user_id'        => $u->id,
                    'type'           => 'laundry_ready',
                    'title'          => 'Laundry Ready for Delivery',
                    'body'           => "Order {$laundryOrder->order_number} — Room {$laundryOrder->room_number} is ready.",
                    'reference_type' => 'laundry_order',
                    'reference_id'   => $laundryOrder->id,
                    'action_url'     => route('laundry.orders.show', $laundryOrder->id),
                    'created_at'     => now(),
                ]));
        }

        // Send SMS/Email notification to guest/walk-in that laundry is ready
        $guest = $laundryOrder->guest;
        \App\Jobs\SendLaundryReadyJob::dispatch([
            'order_number' => $laundryOrder->order_number,
            'email'        => $guest?->email ?? null,
            'phone'        => $guest?->phone_number ?? $laundryOrder->walkin_phone ?? null,
            'room_number'  => $laundryOrder->room_number ?? null,
            'total'        => $laundryOrder->total ?? 0,
        ])->onQueue('notifications');

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} is ready.");
    }

    // POST /laundry/orders/{laundryOrder}/deliver
    public function deliver(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'ready', 422, 'Order must be ready before delivery.');
        abort_if($laundryOrder->customer_type !== 'guest', 422, 'Delivery is only for hotel guest orders.');

        $laundryOrder->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
            'delivered_by' => auth()->id(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} delivered to Room {$laundryOrder->room_number}.");
    }

    // POST /laundry/orders/{laundryOrder}/collected
    public function collected(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'ready', 422, 'Order must be ready before collection.');
        abort_if($laundryOrder->customer_type !== 'walkin', 422, 'Collected is only for walk-in orders.');

        $laundryOrder->update([
            'status'       => 'collected',
            'collected_at' => now(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} collected by {$laundryOrder->customer_name}.");
    }

    // POST /laundry/orders/{laundryOrder}/settle
    public function settle(Request $request, LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status === 'settled',   422, 'Order already settled.');
        abort_if($laundryOrder->status === 'cancelled', 422, 'Cannot settle a cancelled order.');

        $request->validate([
            'payment_method' => 'required|in:cash,card,charge_to_booking',
            'discount'       => 'nullable|numeric|min:0',
            'booking_id'     => 'required_if:payment_method,charge_to_booking|nullable|uuid',
        ]);

        DB::transaction(function () use ($request, $laundryOrder) {

            // Apply discount if provided
            if ($request->filled('discount') && $request->discount > 0) {
                $laundryOrder->update(['discount' => $request->discount]);
                $laundryOrder->load('items');
                $laundryOrder->recalculate();
                $laundryOrder->refresh();
            }

            $laundryOrder->update([
                'status'         => 'settled',
                'payment_method' => $request->payment_method,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
                'booking_id'     => $request->booking_id ?? $laundryOrder->booking_id,
            ]);

            // Post to accounting journal (if cash/card settlement)
            if (in_array($request->payment_method, ['cash', 'card'])) {
                app(AccountingService::class)->postLaundrySettlement(
                    orderNo: $laundryOrder->order_number,
                    orderId: $laundryOrder->id,
                    amount: (float) $laundryOrder->total,
                    paymentMethod: $request->payment_method,
                    actorId: auth()->id()
                );
            }

            // Guest charge to booking → create BookingCharge
            if ($request->payment_method === 'charge_to_booking') {
                $bookingId = $request->booking_id ?? $laundryOrder->booking_id;
                abort_if(!$bookingId, 422, 'Booking ID is required to charge to booking.');

                BookingCharge::create([
                    'booking_id'   => $bookingId,
                    'charge_type'  => 'laundry',
                    'source'       => 'laundry',
                    'reference_id' => $laundryOrder->id,
                    'description'  => "Laundry Order {$laundryOrder->order_number} — " .
                                      $laundryOrder->items->count() . " item(s)",
                    'amount'       => $laundryOrder->total,
                    'currency'     => 'TZS',
                    'status'       => 'unpaid',
                    'created_by'   => auth()->id(),
                ]);
            }

            // Walk-in cash/card → create FinancePayment + FinancialTransaction
            if (in_array($request->payment_method, ['cash', 'card'])) {
                $exchangeRate = (float) (SystemSetting::where('key', 'tzs_exchange_rate')->value('value') ?? 2500);
                $amountUsd    = FinancePayment::toUsd($laundryOrder->total, 'TZS', $exchangeRate);

                $payment = FinancePayment::create([
                    'type'            => 'walkin',
                    'checkout_id'     => null,
                    'order_id'        => null,
                    'method'          => $request->payment_method,
                    'currency'        => 'TZS',
                    'amount_tendered' => $laundryOrder->total,
                    'amount_usd'      => $amountUsd,
                    'exchange_rate'   => $exchangeRate,
                    'status'          => 'completed',
                    'note'            => "Laundry Order {$laundryOrder->order_number}",
                    'created_by'      => auth()->id(),
                ]);

                FinancialTransaction::record([
                    'finance_payment_id' => $payment->id,
                    'type'               => 'income',
                    'category'           => 'laundry',
                    'description'        => "Laundry walk-in payment — {$laundryOrder->order_number}",
                    'amount_usd'         => $amountUsd,
                    'currency'           => 'TZS',
                    'amount_original'    => $laundryOrder->total,
                    'exchange_rate'      => $exchangeRate,
                ], auth()->id());
            }
        });

        // Award loyalty points for laundry (30 points per 10,000 TZS)
        $freshOrder = $laundryOrder->fresh();
        if ($freshOrder->guest_id && $freshOrder->guest) {
            $pointsEarned = (int) floor(($freshOrder->total ?? 0) / 10000) * 30;
            if ($pointsEarned > 0) {
                $freshOrder->guest->addPoints($pointsEarned, 'laundry', $freshOrder->id);
            }
        }

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} settled. Total: " .
                              number_format($freshOrder->total, 2) . ' TZS');
    }

    // POST /laundry/orders/{laundryOrder}/cancel
    public function cancel(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status === 'settled', 422, 'Cannot cancel a settled order.');

        $laundryOrder->update(['status' => 'cancelled']);

        return redirect()
            ->route('laundry.orders.index')
            ->with('success', "Order {$laundryOrder->order_number} cancelled.");
    }
}
