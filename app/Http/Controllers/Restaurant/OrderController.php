<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AccountingService;

class OrderController extends Controller
{
    /**
     * GET /restaurant/orders
     */
    public function index(Request $request): View
    {
        $orders = Order::with(['table', 'location', 'items', 'creator'])
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->when($request->date,        fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(30);

        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        return view('restaurant.orders.index', compact('orders', 'locations'));
    }

    /**
     * GET /restaurant/orders/create
     */
    public function create(Request $request): View
    {
        $locations  = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();
        $tables     = Table::where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->get();
        $categories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->where('is_available', true)])
                          ->where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->get();

        return view('restaurant.orders.create', compact('locations', 'tables', 'categories'));
    }

    /**
     * POST /restaurant/orders
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_id'          => 'required|uuid|exists:stock_locations,id',
            'table_id'             => 'nullable|uuid|exists:tables,id',
            'order_type'           => 'required|in:guest,walkin',
            'booking_id'           => 'required_if:order_type,guest|nullable|uuid',
            'customer_name'        => 'required_if:order_type,walkin|nullable|string|max:150',
            'notes'                => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.notes'        => 'nullable|string|max:255',
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'location_id'   => $data['location_id'],
                'table_id'      => $data['table_id'] ?? null,
                'order_type'    => $data['order_type'],
                'booking_id'    => $data['booking_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'status'        => 'open',
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $menuItem->selling_price,
                    'subtotal'     => $menuItem->selling_price * $item['quantity'],
                    'notes'        => $item['notes'] ?? null,
                    'status'       => 'pending',
                ]);
            }

            // Mark table as occupied
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            $order->recalculate();

            return $order;
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    /**
     * GET /restaurant/orders/{order}
     */
    public function show(Order $order): View
    {
        $order->load(['items.menuItem.ingredients.product', 'table', 'location', 'creator', 'settler']);

        return view('restaurant.orders.show', compact('order'));
    }

    /**
     * POST /restaurant/orders/{order}/send
     */
    public function send(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Only open orders can be sent.');

        $order->update(['status' => 'sent']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order sent to preparation.');
    }

    /**
     * POST /restaurant/orders/{order}/ready
     */
    public function ready(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'sent', 422, 'Order must be sent before marking ready.');

        $order->update(['status' => 'ready']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as ready.');
    }

    /**
     * POST /restaurant/orders/{order}/serve
     */
    public function serve(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'ready', 422, 'Order must be ready before serving.');

        $order->update(['status' => 'served']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as served.');
    }

    /**
     * POST /restaurant/orders/{order}/settle
     * This is where stock is deducted.
     */
    public function settle(Request $request, Order $order): RedirectResponse
    {
        abort_if(!in_array($order->status, ['served', 'ready', 'sent', 'open']), 422, 'Order cannot be settled.');
        abort_if($order->status === 'settled', 422, 'Order already settled.');

        $request->validate([
            'payment_method' => 'required|in:cash,card,charge_to_booking',
            'booking_id'     => 'required_if:payment_method,charge_to_booking|nullable|uuid',
        ]);

        DB::transaction(function () use ($request, $order) {

            // 1. Deduct stock for every order item that has ingredients
            $order->load('items.menuItem.ingredients');

            foreach ($order->items as $orderItem) {
                if ($orderItem->status === 'cancelled') continue;

                foreach ($orderItem->menuItem->ingredients as $ingredient) {
                    $locationId = $order->location_id;
                    StockMovement::record([
                        'product_id'     => $ingredient->product_id,
                        'location_id'    => $locationId,
                        'type'           => 'recipe_use',
                        'quantity'       => $ingredient->quantity * $orderItem->quantity,
                        'reference_type' => 'order',
                        'reference_id'   => $order->id,
                        'notes'          => "Order {$order->order_number} — {$orderItem->menuItem->name} x{$orderItem->quantity}",
                    ], auth()->id());
                }
            }

            // 2. Mark order as settled
            $order->update([
                'status'         => 'settled',
                'payment_method' => $request->payment_method,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
                'booking_id'     => $request->booking_id ?? $order->booking_id,
            ]);

            // 3. Post to accounting journal (if cash/card settlement)
            if (in_array($request->payment_method, ['cash', 'card'])) {
                app(AccountingService::class)->postRestaurantSettlement(
                    orderNo: $order->order_number,
                    orderId: $order->id,
                    amount: (float) $order->total,
                    paymentMethod: $request->payment_method,
                    actorId: auth()->id()
                );
            }

            // 4. If charge to booking — create booking charge record
            if ($request->payment_method === 'charge_to_booking') {
                $bookingId = $request->booking_id ?? $order->booking_id;
                BookingCharge::create([
                    'booking_id'   => $bookingId,
                    'order_id'     => $order->id,
                    'charge_type'  => 'restaurant',
                    'reference_id' => $order->id,
                    'description'  => "Restaurant order {$order->order_number}",
                    'amount'       => $order->total,
                    'status'       => 'unpaid',
                    'created_by'   => auth()->id(),
                ]);
            }

            // 5. Free up the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        // Award loyalty points for restaurant (50 points per 10,000 TZS)
        $freshOrder = $order->fresh();
        if ($freshOrder->booking_id) {
            $booking = \App\Models\Booking::with('guest')->find($freshOrder->booking_id);
            if ($booking && $booking->guest) {
                $pointsEarned = (int) floor(($freshOrder->total ?? 0) / 10000) * 50;
                if ($pointsEarned > 0) {
                    $booking->guest->addPoints($pointsEarned, 'restaurant', $freshOrder->id);
                }
            }
        }

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "Order {$order->order_number} settled successfully.");
    }

    /**
     * POST /restaurant/orders/{order}/cancel
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->status === 'settled', 422, 'Cannot cancel a settled order.');

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Free the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        return redirect()
            ->route('restaurant.orders.index')
            ->with('success', "Order {$order->order_number} cancelled.");
    }

    /**
     * POST /restaurant/orders/{order}/items
     * Add item to an open order.
     */
    public function addItem(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only add items to open orders.');

        $request->validate([
            'menu_item_id' => 'required|uuid|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string|max:255',
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        DB::transaction(function () use ($request, $order, $menuItem) {
            // If item already in order, increase quantity
            $existing = $order->items()->where('menu_item_id', $menuItem->id)->first();

            if ($existing) {
                $existing->update([
                    'quantity' => $existing->quantity + $request->quantity,
                    'subtotal' => ($existing->quantity + $request->quantity) * $existing->unit_price,
                    'notes'    => $request->notes ?? $existing->notes,
                ]);
            } else {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $request->quantity,
                    'unit_price'   => $menuItem->selling_price,
                    'subtotal'     => $menuItem->selling_price * $request->quantity,
                    'notes'        => $request->notes,
                    'status'       => 'pending',
                ]);
            }

            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "{$menuItem->name} added to order.");
    }

    /**
     * DELETE /restaurant/orders/{order}/items/{orderItem}
     * Remove item from open order.
     */
    public function removeItem(Order $order, OrderItem $orderItem): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only remove items from open orders.');

        DB::transaction(function () use ($order, $orderItem) {
            $orderItem->update(['status' => 'cancelled']);
            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Item removed from order.');
    }
}
