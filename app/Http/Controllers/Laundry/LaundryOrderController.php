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
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaundryOrderController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

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
        $userIds = User::whereHas('role', fn ($q) => $q->whereIn('name', ['supervisor', 'laundry_manager']))
            ->pluck('id')
            ->toArray();

        $this->notificationService->createForUsers($userIds, [
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
        ]);

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
            $frontDeskIds = User::whereHas('role', fn ($q) => $q->where('name', 'front_desk'))
                ->pluck('id')
                ->toArray();

            $this->notificationService->createForUsers($frontDeskIds, [
                'type'           => 'laundry_ready',
                'title'          => 'Laundry Ready for Delivery',
                'body'           => "Order {$laundryOrder->order_number} — Room {$laundryOrder->room_number} is ready.",
                'reference_type' => 'laundry_order',
                'reference_id'   => $laundryOrder->id,
                'action_url'     => route('laundry.orders.show', $laundryOrder->id),
            ]);
        }

        // Send SMS/Email notification to guest/walk-in that laundry is ready
        $guest = $laundryOrder->booking?->guest;
        \App\Jobs\SendLaundryReadyJob::dispatch([
            'order_number' => $laundryOrder->order_number,
            'email'        => $guest?->email ?? null,
            'phone'        => $guest?->phone_number ?? $laundryOrder->customer_phone ?? null,
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

    /**
     * POST /laundry/orders/{laundryOrder}/settle
     * 
     * UNIFIED CHECKOUT FLOW:
     * - Guest orders: Create BookingCharge and redirect to Finance Checkout
     * - Walk-in orders: Use WalkinPaymentController (direct payment modal)
     */
    public function settle(Request $request, LaundryOrder $laundryOrder): RedirectResponse
    {
        // Prevent re-settlement of already charged or settled orders
        abort_if(in_array($laundryOrder->status, ['charged', 'settled']), 422, 'Order already charged or settled.');
        abort_if($laundryOrder->status === 'cancelled', 422, 'Cannot settle a cancelled order.');

        // For guest orders, we ONLY allow charge_to_booking (enforces checkout flow)
        // Walk-in direct payments are handled by WalkinPaymentController
        if ($laundryOrder->customer_type === 'guest') {
            $request->validate([
                'discount'   => 'nullable|numeric|min:0',
                'booking_id' => 'nullable|uuid|exists:bookings,id',
            ]);
            
            $bookingId = $request->booking_id ?? $laundryOrder->booking_id;
            abort_if(!$bookingId, 422, 'Booking ID is required for guest laundry orders.');
        } else {
            // Walk-in orders can settle directly (handled by WalkinPaymentController)
            // This path should NOT be used for walk-ins - they use the modal
            abort(422, 'Walk-in orders must be settled through the payment modal.');
        }

        DB::transaction(function () use ($request, $laundryOrder, $bookingId) {

            // Apply discount if provided
            // ROLE RESTRICTION: Only LAUNDRY_MANAGER and MANAGER can apply discounts
            if ($request->filled('discount') && $request->discount > 0) {
                // Check if user has permission to apply discount
                $user = auth()->user();
                $canApplyDiscount = $user->hasAnyRole(['laundry_manager', 'manager']);
                
                if (!$canApplyDiscount) {
                    abort(403, 'Only Laundry Manager or Manager can apply discounts to laundry orders.');
                }
                
                $laundryOrder->update(['discount' => $request->discount]);
                $laundryOrder->load('items');
                $laundryOrder->recalculate();
                $laundryOrder->refresh();
            }

            // Mark order as charged (NOT settled - will be settled at checkout)
            $laundryOrder->update([
                'status'         => 'charged',
                'payment_method' => 'charge_to_booking',
                'booking_id'     => $bookingId,
            ]);

            // Create BookingCharge — payment will happen at Finance Checkout
            // Store amount in USD (converted from TZS) and also store TZS amount
            $exchangeRate = (float) (DB::table('system_settings')
                ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500);
            $amountUsd = round($laundryOrder->total / $exchangeRate, 2);

            BookingCharge::create([
                'booking_id'   => $bookingId,
                'charge_type'  => 'laundry',
                'source'       => 'laundry',
                'reference_id' => $laundryOrder->id,
                'description'  => "Laundry Order {$laundryOrder->order_number} — " .
                                  $laundryOrder->items->count() . " item(s)",
                'amount'       => $amountUsd,
                'currency'     => 'USD',
                'amount_tzs'   => $laundryOrder->total,
                'status'       => 'unpaid',
                'created_by'   => auth()->id(),
            ]);
        });

        // Award loyalty points for laundry (30 points per 10,000 TZS)
        $freshOrder = $laundryOrder->fresh();
        if ($freshOrder->booking_id) {
            $booking = Booking::with('guest')->find($freshOrder->booking_id);
            if ($booking && $booking->guest) {
                $pointsEarned = (int) floor(($freshOrder->total ?? 0) / 10000) * 30;
                if ($pointsEarned > 0) {
                    $booking->guest->addPoints($pointsEarned, 'laundry', $freshOrder->id);
                }
            }
        }

        // Redirect to Finance Checkout page
        return redirect()
            ->route('finance.checkout.show', $bookingId)
            ->with('success', "Laundry order {$laundryOrder->order_number} charged to booking. Please complete payment at checkout.");
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
