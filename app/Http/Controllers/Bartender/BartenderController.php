<?php

namespace App\Http\Controllers\Bartender;

use App\Http\Controllers\Controller;
use App\Models\BarDamageReport;
use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Services\Billing\ModuleBillingService;
use App\Services\Bartender\BarOrderStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BartenderController extends Controller
{
    public function __construct(
        protected BarOrderStockService $barOrderStockService,
        protected ModuleBillingService $moduleBillingService
    ) {
    }

    public function dashboard(): View
    {
        $bar = StockLocation::bar();

        $stats = [
            'pending_orders' => Order::where('location_id', $bar->id)
                ->whereIn('bartender_status', ['pending', 'accepted', 'prepared'])
                ->count(),
            'damage_reports_today' => BarDamageReport::where('location_id', $bar->id)
                ->whereDate('reported_at', today())
                ->count(),
            'available_items' => StockLevel::where('location_id', $bar->id)
                ->where('quantity', '>', 0)
                ->count(),
        ];

        return view('bartender.dashboard', compact('stats'));
    }

    public function stock(Request $request): View
    {
        $bar = StockLocation::bar();

        $levels = StockLevel::with('product')
            ->where('location_id', $bar->id)
            ->when($request->search, fn ($q) => $q->whereHas('product', function ($pq) use ($request) {
                $pq->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            }))
            ->orderByDesc('updated_at')
            ->paginate(25);

        return view('bartender.stock', compact('levels', 'bar'));
    }

    public function inbox(Request $request): View
    {
        $bar = StockLocation::bar();

        $orders = Order::with(['items.menuItem', 'booking'])
            ->where('location_id', $bar->id)
            ->whereNotNull('order_source')
            ->when($request->source, fn ($q) => $q->where('order_source', $request->source))
            ->when($request->status, fn ($q) => $q->where('bartender_status', $request->status))
            ->latest()
            ->paginate(20);

        return view('bartender.inbox', compact('orders'));
    }

    public function showOrder(Order $order): View
    {
        $bar = StockLocation::bar();
        abort_if($order->location_id !== $bar->id, 404);

        $order->load(['items.menuItem.ingredients.product', 'booking']);
        $availability = $this->barOrderStockService->checkAvailability($order);

        return view('bartender.order-show', compact('order', 'availability'));
    }

    public function pos(): View
    {
        $bar = StockLocation::bar();

        // Ensure all bar Products have corresponding MenuItems
        $this->syncBarProductsToMenu($bar);

        $categories = MenuCategory::with(['menuItems' => fn ($q) => $q->where('is_active', true)->where('is_available', true)])
            ->where('location_id', $bar->id)
            ->where('is_active', true)
            ->get();

        $bookings = Booking::query()
            ->where('status', 'checked_in')
            ->with('room')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('bartender.pos', compact('categories', 'bar', 'bookings'));
    }

    protected function syncBarProductsToMenu(StockLocation $bar): void
    {
        $barProducts = Product::query()
            ->where('product_type', 'bar')
            ->where('is_active', true)
            ->get();

        if ($barProducts->isEmpty()) {
            return;
        }

        $defaultCategory = MenuCategory::query()
            ->where('location_id', $bar->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        if (!$defaultCategory) {
            return;
        }

        foreach ($barProducts as $product) {
            $existing = MenuItem::where('name', $product->name)
                ->where('category_id', $defaultCategory->id)
                ->exists();

            if ($existing) {
                continue;
            }

            MenuItem::create([
                'category_id'   => $defaultCategory->id,
                'name'          => $product->name,
                'description'   => $product->description,
                'selling_price' => $product->selling_price,
                'is_available'  => true,
                'is_active'     => true,
                'varieties'     => $product->varieties,
                'created_by'    => $product->created_by ?? auth()->id(),
            ]);
        }
    }

    public function storePos(Request $request): RedirectResponse
    {
        $bar = StockLocation::bar();

        $data = $request->validate([
            'customer_type' => 'required|in:walkin,guest',
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'booking_id' => 'nullable|uuid|exists:bookings,id',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($data, $bar) {
            $isGuest = $data['customer_type'] === 'guest' && !empty($data['booking_id']);
            $bookingId = $isGuest ? $data['booking_id'] : null;
            $customerName = null;
            $customerPhone = null;

            if ($isGuest) {
                $booking = Booking::with('room')->findOrFail($data['booking_id']);
                $customerName = $booking->guest_display_name;
            } else {
                $customerName = $data['customer_name'] ?: 'Walk-in Guest';
                $customerPhone = $data['customer_phone'] ?? null;
            }

            $order = Order::create([
                'location_id' => $bar->id,
                'order_type' => $isGuest ? 'guest' : 'walkin',
                'order_source' => $isGuest ? 'room_service' : 'walkin',
                'booking_id' => $bookingId,
                'bartender_status' => 'pending',
                'bartender_status_updated_at' => now(),
                'status' => 'open',
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'notes' => $data['notes'] ?? null,
                'created_by' => $this->actorId(),
            ]);

            foreach ($data['items'] as $line) {
                $menuItem = MenuItem::findOrFail($line['menu_item_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $menuItem->selling_price,
                    'subtotal' => $menuItem->selling_price * $line['quantity'],
                    'notes' => null,
                    'status' => 'pending',
                ]);
            }

            $order->recalculate();

            return $order;
        });

        return redirect()->route('bartender.orders.show', $order)
            ->with('success', "Order {$order->order_number} created successfully.");
    }

    public function walkinSalesReport(Request $request): View
    {
        $bar = StockLocation::bar();

        $orders = Order::with(['items.menuItem', 'settler'])
            ->where('location_id', $bar->id)
            ->where('order_source', 'walkin')
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->payment_method, fn ($q) => $q->where('payment_method', $request->payment_method))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $baseSummary = Order::query()
            ->where('location_id', $bar->id)
            ->where('order_source', 'walkin')
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->payment_method, fn ($q) => $q->where('payment_method', $request->payment_method));

        $summary = [
            'count' => (clone $baseSummary)->count(),
            'total' => (float) (clone $baseSummary)->sum('total'),
            'paid' => (float) (clone $baseSummary)->where('status', 'settled')->sum('total'),
        ];

        return view('bartender.walkin-sales', compact('orders', 'summary'));
    }

    public function acceptOrder(Order $order): RedirectResponse
    {
        return $this->transitionWithAvailabilityCheck($order, 'accepted', ['pending']);
    }

    public function prepareOrder(Order $order): RedirectResponse
    {
        return $this->transitionStatus($order, 'prepared', ['accepted']);
    }

    public function serveOrder(Order $order): RedirectResponse
    {
        $bar = StockLocation::bar();
        abort_if($order->location_id !== $bar->id, 404);
        abort_if($order->order_source === 'walkin', 422, 'Walk-in orders are marked served during payment settlement.');
        abort_if(in_array($order->status, ['cancelled', 'settled'], true), 422, 'This order cannot be served.');

        abort_if($order->bartender_status !== 'prepared', 422, 'Order must be prepared before serving.');

        $availability = $this->barOrderStockService->checkAvailability($order);
        abort_if(!$availability['ok'], 422, $availability['errors'][0]['message'] ?? 'Insufficient stock for this order.');

        try {
            DB::transaction(function () use ($order) {
                $this->barOrderStockService->deductForOrder($order, $this->actorId());

                $order->update([
                    'bartender_status' => 'served',
                    'bartender_status_updated_at' => now(),
                    'status' => 'served',
                ]);

                if ($order->order_source === 'room_service') {
                    $this->moduleBillingService->syncOrderCharge($order->fresh(), $this->actorId());
                }
            });
        } catch (\Throwable $e) {
            Log::error('Bartender serve flow failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            $order->update([
                'billing_error' => $e->getMessage(),
            ]);

            return redirect()->route('bartender.orders.show', $order)
                ->with('error', 'Unable to complete serving. Please retry or notify a manager.');
        }

        return redirect()->route('bartender.orders.show', $order)
            ->with('success', "Order {$order->order_number} marked as served.");
    }

    public function rejectOrder(Order $order): RedirectResponse
    {
        return $this->transitionStatus($order, 'rejected', ['pending']);
    }

    public function cancelOrder(Order $order): RedirectResponse
    {
        $bar = StockLocation::bar();
        abort_if($order->location_id !== $bar->id, 404);
        abort_if($order->status === 'settled', 422, 'Paid orders cannot be cancelled from bartender desk.');
        abort_if($order->charge?->isPaid(), 422, 'Orders with paid charges cannot be cancelled from bartender desk.');

        if ($order->stock_deducted_at && !$order->stock_reversed_at) {
            $this->barOrderStockService->reverseForCancelledOrder($order, $this->actorId());
        }

        $this->moduleBillingService->voidChargeForOrder($order);

        $order->update([
            'bartender_status' => 'cancelled',
            'bartender_status_updated_at' => now(),
            'status' => 'cancelled',
        ]);

        return redirect()->route('bartender.inbox')
            ->with('success', "Order {$order->order_number} cancelled.");
    }

    public function damageForm(): View
    {
        $bar = StockLocation::bar();
        $products = Product::where('is_active', true)
            ->whereHas('stockLevels', fn ($q) => $q->where('location_id', $bar->id))
            ->orderBy('name')
            ->get();

        return view('bartender.damage-form', compact('products', 'bar'));
    }

    public function reportDamage(Request $request): RedirectResponse
    {
        $bar = StockLocation::bar();

        $data = $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'reason' => 'required|in:spillage,breakage,expired,other',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $bar) {
            $movement = StockMovement::record([
                'product_id' => $data['product_id'],
                'location_id' => $bar->id,
                'type' => 'damage',
                'quantity' => $data['quantity'],
                'reference_type' => 'bar_damage_report',
                'notes' => strtoupper($data['reason']) . ($data['notes'] ? ' | ' . $data['notes'] : ''),
            ], $this->actorId());

            BarDamageReport::create([
                'product_id' => $data['product_id'],
                'location_id' => $bar->id,
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'reported_by' => $this->actorId(),
                'reported_at' => now(),
                'stock_movement_id' => $movement->id,
            ]);
        });

        return redirect()->route('bartender.damage.index')
            ->with('success', 'Damage report submitted and stock deducted.');
    }

    public function damageIndex(): View
    {
        $bar = StockLocation::bar();

        $reports = BarDamageReport::with(['product', 'reporter'])
            ->where('location_id', $bar->id)
            ->latest('reported_at')
            ->paginate(25);

        return view('bartender.damage-index', compact('reports'));
    }

    protected function transitionWithAvailabilityCheck(Order $order, string $target, array $allowed): RedirectResponse
    {
        $bar = StockLocation::bar();
        abort_if($order->location_id !== $bar->id, 404);
        abort_if(in_array($order->status, ['cancelled', 'settled'], true), 422, 'This order is no longer actionable.');
        abort_if(!in_array($order->bartender_status, $allowed, true), 422, 'Invalid order status transition.');

        $availability = $this->barOrderStockService->checkAvailability($order);
        abort_if(!$availability['ok'], 422, $availability['errors'][0]['message'] ?? 'Insufficient stock for this order.');

        $order->update([
            'bartender_status' => $target,
            'bartender_status_updated_at' => now(),
        ]);

        return redirect()->route('bartender.orders.show', $order)
            ->with('success', "Order {$order->order_number} marked {$target}.");
    }

    protected function transitionStatus(Order $order, string $target, array $allowed): RedirectResponse
    {
        $bar = StockLocation::bar();
        abort_if($order->location_id !== $bar->id, 404);
        abort_if(in_array($order->status, ['cancelled', 'settled'], true), 422, 'This order is no longer actionable.');
        abort_if(!in_array($order->bartender_status, $allowed, true), 422, 'Invalid order status transition.');

        $order->update([
            'bartender_status' => $target,
            'bartender_status_updated_at' => now(),
        ]);

        return redirect()->route('bartender.orders.show', $order)
            ->with('success', "Order {$order->order_number} marked {$target}.");
    }

    protected function actorId(): string
    {
        return (string) Auth::id();
    }
}
