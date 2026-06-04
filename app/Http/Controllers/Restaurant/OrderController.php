<?php

namespace App\Http\Controllers\Restaurant;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuOptionValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLocation;
use App\Models\Table;
use App\Services\Billing\ModuleBillingService;
use App\Services\Bartender\BarOrderStockService;
use App\Services\AccountingService;      // Added for POS accounting posting
use App\Services\ReceiptService;          // Added for POS receipt creation
use App\Services\OrderDispatchService;
use App\Models\BuffetPackage;
use App\Models\BuffetSale;
use App\Models\FinancePayment;            // Added for POS walk-in payments
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * GET /restaurant/orders
     */
    public function index(Request $request): View
    {
        $orders = Order::with(['table', 'location', 'items', 'creator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
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
        $kitchen = StockLocation::kitchen();
        $tables     = Table::where('is_active', true)
                          ->when($kitchen, fn($q) => $q->where('location_id', $kitchen->id))
                          ->get();
        $categories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->with([
            'optionGroups' => fn($g) => $g->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])])
                          ->where('is_active', true)
                          ->orderBy('sort_order')
                          ->orderBy('name')
                          ->get();

        return view('restaurant.orders.create', compact('kitchen', 'tables', 'categories'));
    }

    /**
     * GET /restaurant/pos
     * Restaurant POS screen — grid-based Quick Sale interface modeled on bar POS.
     */
    public function pos(Request $request): View
    {
        $kitchen = StockLocation::kitchen();

        $categories = MenuCategory::with(['menuItems' => function ($q) {
            $q->where('is_active', true)
              ->where('is_available', true)
              ->where('is_buffet', false)
              ->where(function ($sub) {
                  $sub->whereNull('available_from')
                      ->orWhere('available_from', '<=', now()->format('H:i:s'));
              })
              ->where(function ($sub) {
                  $sub->whereNull('available_until')
                      ->orWhere('available_until', '>=', now()->format('H:i:s'));
              })
              ->with(['optionGroups' => fn($g) => $g->where('is_active', true)->with([
                  'values' => fn($v) => $v->where('is_active', true),
              ])]);
        }])
            ->where('is_active', true)
            ->when($kitchen, fn($q) => $q->where('location_id', $kitchen->id))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Filter out categories with no visible items
        $categories = $categories->filter(fn($cat) => $cat->menuItems->isNotEmpty())->values();

        // Build stock lookup keyed by product name (lowercase) → available quantity
        $stockLevels = \App\Models\StockLevel::with('product')
            ->when($kitchen, fn($q) => $q->where('location_id', $kitchen->id))
            ->where('quantity', '>', 0)
            ->get();

        $stockMap = [];
        foreach ($stockLevels as $level) {
            if ($level->product) {
                $name = mb_strtolower(trim($level->product->name));
                $stockMap[$name] = (float) $level->available_qty;
            }
        }

        // Build image map for menu items that have media
        $imageMap = [];
        foreach ($categories as $cat) {
            foreach ($cat->menuItems as $item) {
                if ($item->hasMedia('menu_item_image')) {
                    $url = $item->getFirstMediaUrl('menu_item_image', 'thumb')
                        ?: $item->getFirstMediaUrl('menu_item_image');
                    if ($url) {
                        $imageMap[$item->id] = $url;
                    }
                }
            }
        }

        $buffetPackages = BuffetPackage::where('is_active', true)->orderBy('name')->get();

        $recentOrders = Order::with(['items.menuItem', 'table', 'location', 'creator'])
            ->where('status', 'served')
            ->latest()
            ->get();

        return view('restaurant.pos', compact('categories', 'stockMap', 'imageMap', 'buffetPackages', 'recentOrders'));
    }

    /**
     * POST /restaurant/pos
     * Process restaurant POS sale — walk-in or guest folio charge.
     */
    public function storePos(Request $request): RedirectResponse
    {
        $kitchen = StockLocation::kitchen();
        abort_if(!$kitchen, 500, 'Kitchen stock location not configured.');

        $data = $request->validate([
            'existing_order_id' => 'nullable|uuid|exists:orders,id',
            'customer_name'  => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'payment_method' => 'required|in:cash,mobile,card,charge_to_booking',
            'booking_id'     => 'required_if:payment_method,charge_to_booking|uuid|exists:bookings,id',
            'notes'          => 'nullable|string|max:500',
            'items'          => 'nullable|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'buffet_items'          => 'nullable|array',
            'buffet_items.*.buffet_package_id' => 'required|uuid|exists:buffet_packages,id',
            'buffet_items.*.adults'            => 'required|integer|min:1',
            'buffet_items.*.children'          => 'nullable|integer|min:0',
        ]);

        // Finalise existing served order
        if (!empty($data['existing_order_id'])) {
            return $this->finaliseExistingOrder($data);
        }

        $hasItems = !empty($data['items']);
        $hasBuffet = !empty($data['buffet_items']);

        abort_if(!$hasItems && !$hasBuffet, 422, 'At least one item or buffet package is required.');

        // Validate the booking is checked in if charging to folio
        $isChargeToBooking = $data['payment_method'] === 'charge_to_booking';

        if ($isChargeToBooking) {
            $booking = \App\Models\Booking::findOrFail($data['booking_id']);
            abort_if($booking->status !== 'checked_in', 422, 'Can only charge to a checked-in booking.');
        }

        $order = DB::transaction(function () use ($data, $kitchen, $isChargeToBooking, $hasItems, $hasBuffet) {
            $customerName = $data['customer_name'] ?: ($isChargeToBooking ? 'Guest' : 'Walk-in Guest');
            $customerPhone = $data['customer_phone'] ?? null;

            $order = null;
            $buffetSales = [];

            if ($hasItems) {
                $order = Order::create([
                    'location_id'    => $kitchen->id,
                    'order_type'     => $isChargeToBooking ? 'guest' : 'walkin',
                    'order_source'   => 'walkin',
                    'booking_id'     => $isChargeToBooking ? $data['booking_id'] : null,
                    'customer_name'  => $customerName,
                    'customer_phone' => $customerPhone,
                    'status'         => 'open',
                    'payment_method' => $data['payment_method'],
                    'notes'          => $data['notes'] ?? null,
                    'created_by'     => (string) Auth::id(),
                ]);

                foreach ($data['items'] as $line) {
                    $menuItem = MenuItem::findOrFail($line['menu_item_id']);
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'menu_item_id'   => $menuItem->id,
                        'item_name_snapshot' => $menuItem->name,
                        'quantity'       => $line['quantity'],
                        'base_unit_price' => (float) $menuItem->selling_price,
                        'unit_price'     => (float) $menuItem->selling_price,
                        'subtotal'       => (float) $menuItem->selling_price * $line['quantity'],
                        'status'         => 'pending',
                    ]);
                }

                $order->recalculate();
                app(BarOrderStockService::class)->deductForOrder($order, (string) Auth::id());
            }

            if ($hasBuffet) {
                foreach ($data['buffet_items'] as $bi) {
                    $package = BuffetPackage::findOrFail($bi['buffet_package_id']);
                    $children = (int) ($bi['children'] ?? 0);
                    $total = ((int) $bi['adults'] * (float) $package->adult_price)
                        + ($children * (float) $package->child_price);

                    $sale = BuffetSale::create([
                        'buffet_package_id'    => $package->id,
                        'booking_id'           => $isChargeToBooking ? ($data['booking_id'] ?? null) : null,
                        'sale_type'            => $isChargeToBooking ? 'booking' : 'walkin',
                        'adults_count'         => (int) $bi['adults'],
                        'children_count'       => $children,
                        'package_name_snapshot' => $package->name,
                        'adult_price_snapshot'  => $package->adult_price,
                        'child_price_snapshot'  => $package->child_price,
                        'total_amount'          => $total,
                        'status'                => 'pending',
                        'notes'                 => $data['notes'] ?? null,
                        'served_by'             => (string) Auth::id(),
                    ]);

                    $buffetSales[] = $sale;
                }
            }

            if ($isChargeToBooking) {
                if ($order) {
                    $order->update([
                        'status'             => 'charged',
                        'billed_to_folio_at' => now(),
                    ]);
                    app(ModuleBillingService::class)->syncOrderCharge($order->fresh(), (string) Auth::id());
                }

                foreach ($buffetSales as $sale) {
                    $exchangeRate = CurrencyHelper::getExchangeRate();
                    $amountUsd = round(((float) $sale->total_amount) / $exchangeRate, 2);

                    BookingCharge::updateOrCreate(
                        [
                            'booking_id' => $data['booking_id'],
                            'source' => 'restaurant',
                            'reference_id' => $sale->id,
                        ],
                        [
                            'charge_type' => 'restaurant',
                            'order_id' => null,
                            'description' => "Buffet {$sale->package_name_snapshot} ({$sale->adults_count}A/{$sale->children_count}C)",
                            'amount' => $amountUsd,
                            'currency' => 'USD',
                            'amount_tzs' => $sale->total_amount,
                            'status' => 'unpaid',
                            'created_by' => (string) Auth::id(),
                        ]
                    );

                    $sale->update(['status' => 'charged']);
                }

                app(ReceiptService::class)->getOrCreateReceipt($order ?? $buffetSales[0] ?? null);
            } else {
                // Walk-in — settled immediately
                $paymentMethod = $data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method'];
                $totalAmount = 0;

                if ($order) {
                    $totalAmount += (float) $order->total;
                    $order->update([
                        'status'         => 'settled',
                        'payment_method' => $paymentMethod,
                        'settled_by'     => (string) Auth::id(),
                        'settled_at'     => now(),
                    ]);
                }

                foreach ($buffetSales as $sale) {
                    $totalAmount += (float) $sale->total_amount;
                    $sale->update([
                        'status'           => 'settled',
                        'payment_method'   => $paymentMethod,
                        'settled_by'       => (string) Auth::id(),
                        'settled_at'       => now(),
                    ]);
                }

                if ($totalAmount > 0) {
                    app(AccountingService::class)->postRestaurantSettlement(
                        orderNo: $order?->order_number ?? $buffetSales[0]->sale_number,
                        orderId: $order?->id ?? $buffetSales[0]->id,
                        amount: $totalAmount,
                        paymentMethod: $paymentMethod,
                        actorId: (string) Auth::id()
                    );

                    $exchangeRate = CurrencyHelper::getExchangeRate();
                    $amountUsd = FinancePayment::toUsd($totalAmount, 'TZS', $exchangeRate);

                    FinancePayment::create([
                        'payment_type'  => 'walkin',
                        'checkout_id'   => null,
                        'order_id'      => $order?->id,
                        'method'        => $paymentMethod,
                        'amount'        => $totalAmount,
                        'currency'      => 'TZS',
                        'amount_usd'    => $amountUsd,
                        'exchange_rate' => $exchangeRate,
                        'status'        => 'completed',
                        'created_by'    => (string) Auth::id(),
                        'paid_at'       => now(),
                    ]);

                    \App\Models\FinancialTransaction::record([
                        'type'            => 'payment',
                        'source_module'   => 'restaurant',
                        'payment_id'      => null,
                        'order_id'        => $order?->id,
                        'currency'        => 'TZS',
                        'amount'          => $totalAmount,
                        'amount_usd'      => $amountUsd,
                        'exchange_rate'   => $exchangeRate,
                        'payment_method'  => $paymentMethod,
                        'description'     => 'POS sale with ' . count($buffetSales) . ' buffet(s)',
                    ], (string) Auth::id());
                }

                app(ReceiptService::class)->getOrCreateReceipt($order ?? $buffetSales[0] ?? null);
            }

            return ['order' => $order, 'buffetSales' => $buffetSales];
        });

        if ($isChargeToBooking) {
            $msg = $order ? "Order {$order->order_number} " : '';
            $msg .= 'charged to guest folio.';
            if (count($buffetSales) > 0) {
                $msg .= ' ' . count($buffetSales) . ' buffet sale(s) added.';
            }
            $redirectRoute = $order
                ? redirect()->route('restaurant.orders.show', $order)
                : redirect()->route('restaurant.buffet.show', $buffetSales[0]);
            return $redirectRoute->with('success', trim($msg));
        }

        $msg = 'Sale completed successfully.';
        if ($order) $msg = "Order {$order->order_number} " . $msg;
        if (count($buffetSales) > 0) {
            $msg .= ' ' . count($buffetSales) . ' buffet sale(s) created.';
        }
        $redirectRoute = $order
            ? redirect()->route('restaurant.orders.show', $order)
            : redirect()->route('restaurant.buffet.show', $buffetSales[0]);
        return $redirectRoute->with('success', trim($msg));
    }

    /**
     * Finalise an existing served order — process payment and settle.
     */
    protected function finaliseExistingOrder(array $data): RedirectResponse
    {
        $order = Order::with('items.menuItem')->findOrFail($data['existing_order_id']);

        abort_if(!in_array($order->status, ['served', 'ready', 'sent', 'open']), 422, 'Order cannot be finalised. Status must be served.');
        abort_if(in_array($order->status, ['settled', 'charged', 'cancelled']), 422, 'Order already settled or cancelled.');

        $isChargeToBooking = $data['payment_method'] === 'charge_to_booking';

        if ($isChargeToBooking) {
            $booking = \App\Models\Booking::findOrFail($data['booking_id']);
            abort_if($booking->status !== 'checked_in', 422, 'Can only charge to a checked-in booking.');
        }

        DB::transaction(function () use ($order, $data, $isChargeToBooking) {
            $paymentMethod = $isChargeToBooking ? 'charge_to_booking' : ($data['payment_method'] === 'mobile' ? 'mobile_money' : $data['payment_method']);

            if ($isChargeToBooking) {
                $order->update([
                    'status'             => 'charged',
                    'payment_method'     => 'charge_to_booking',
                    'booking_id'         => $data['booking_id'],
                    'billed_to_folio_at' => now(),
                    'settled_by'         => (string) Auth::id(),
                ]);
                app(ModuleBillingService::class)->syncOrderCharge($order->fresh(), (string) Auth::id());
            } else {
                $totalAmount = (float) $order->total;

                $order->update([
                    'status'         => 'settled',
                    'payment_method' => $paymentMethod,
                    'settled_by'     => (string) Auth::id(),
                    'settled_at'     => now(),
                ]);

                app(AccountingService::class)->postRestaurantSettlement(
                    orderNo: $order->order_number,
                    orderId: $order->id,
                    amount: $totalAmount,
                    paymentMethod: $paymentMethod,
                    actorId: (string) Auth::id()
                );

                $exchangeRate = CurrencyHelper::getExchangeRate();
                $amountUsd = FinancePayment::toUsd($totalAmount, 'TZS', $exchangeRate);

                FinancePayment::create([
                    'payment_type'  => 'walkin',
                    'checkout_id'   => null,
                    'order_id'      => $order->id,
                    'method'        => $paymentMethod,
                    'amount'        => $totalAmount,
                    'currency'      => 'TZS',
                    'amount_usd'    => $amountUsd,
                    'exchange_rate' => $exchangeRate,
                    'status'        => 'completed',
                    'created_by'    => (string) Auth::id(),
                    'paid_at'       => now(),
                ]);

                \App\Models\FinancialTransaction::record([
                    'type'            => 'walkin_sale',
                    'source_module'   => 'restaurant',
                    'payment_id'      => null,
                    'order_id'        => $order->id,
                    'currency'        => 'TZS',
                    'amount'          => $totalAmount,
                    'amount_usd'      => $amountUsd,
                    'exchange_rate'   => $exchangeRate,
                    'payment_method'  => $paymentMethod,
                    'description'     => 'POS finalisation of order ' . $order->order_number,
                ], (string) Auth::id());
            }

            app(ReceiptService::class)->getOrCreateReceipt($order);
        });

        if ($isChargeToBooking) {
            return redirect()->route('restaurant.orders.show', $order)
                ->with('success', "Order {$order->order_number} charged to guest folio.");
        }

        return redirect()->route('restaurant.pos')
            ->with('success', "Order {$order->order_number} finalised successfully.");
    }

    /**
     * POST /restaurant/orders
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_id'             => 'nullable|uuid|exists:tables,id',
            'order_type'           => 'required|in:guest,walkin,dine_in,room_service,bar_tab,takeaway',
            'booking_id'           => 'required_if:order_type,guest,room_service|nullable|uuid',
            'customer_name'        => 'required_if:order_type,walkin,takeaway|nullable|string|max:150',
            'notes'                => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.notes'        => 'nullable|string|max:255',
            'items.*.selected_option_value_ids' => 'nullable|array',
            'items.*.selected_option_value_ids.*' => 'uuid|exists:menu_option_values,id',
        ]);

$order = DB::transaction(function () use ($data) {
            $kitchen = StockLocation::kitchen();

            $order = Order::create([
                'location_id'   => $kitchen?->id ?? $data['location_id'] ?? null,
                'table_id'      => $data['table_id'] ?? null,
                'order_type'    => $data['order_type'],
                'order_source'  => null,
                'bartender_status' => null,
                'bartender_status_updated_at' => null,
                'booking_id'    => $data['booking_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'status'        => 'open',
                'notes'         => $data['notes'] ?? null,
                'created_by'    => (string) Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create($this->buildOrderItemPayload($order, $item));
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
        $menuCategories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->with([
            'optionGroups' => fn($g) => $g->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('restaurant.orders.show', compact('order', 'menuCategories'));
    }

    /**
     * POST /restaurant/orders/{order}/send
     */
    public function send(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Only open orders can be sent.');

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'sent']);

            // Dispatch kitchen/bar tickets
            app(OrderDispatchService::class)->splitAndDispatch($order);
        });

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

        DB::transaction(function () use ($order) {
            if ($order->booking_id) {
                app(ModuleBillingService::class)->syncOrderCharge($order, (string) Auth::id());
            }

            $order->update(['status' => 'served']);
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as served.');
    }

    /**
     * POST /restaurant/orders/{order}/settle
     * This is where stock is deducted.
     * 
     * UNIFIED CHECKOUT FLOW:
     * - Guest orders: Create BookingCharge and redirect to Finance Checkout
     * - Walk-in orders: Use WalkinPaymentController (direct payment modal)
     */
    public function settle(Request $request, Order $order): RedirectResponse
    {
        // Prevent re-settlement of already charged or settled orders
        abort_if(in_array($order->status, ['charged', 'settled']), 422, 'Order already charged or settled.');
        abort_if($order->status === 'cancelled', 422, 'Cannot settle a cancelled order.');
        abort_if(!in_array($order->status, ['served', 'ready', 'sent', 'open']), 422, 'Order cannot be settled.');

        // For guest orders, we ONLY allow charge_to_booking (enforces checkout flow)
        // Walk-in direct payments are handled by WalkinPaymentController
        if ($order->order_type === 'guest') {
            if ($this->isBartenderManagedBarOrder($order)) {
                abort_if($order->bartender_status !== 'served', 422, 'Bar items must be served by the bartender before billing to the guest folio.');
            }

            $request->validate([
                'booking_id' => 'required|uuid|exists:bookings,id',
            ]);
            
            // Force charge_to_booking for guest orders
            $paymentMethod = 'charge_to_booking';
        } else {
            // Walk-in orders can settle directly (handled by WalkinPaymentController)
            // This path should NOT be used for walk-ins - they use the modal
            abort(422, 'Walk-in orders must be settled through the payment modal.');
        }

        $bookingId = $request->booking_id ?? $order->booking_id;
        abort_if(!$bookingId, 422, 'Booking ID is required for guest orders.');

        DB::transaction(function () use ($order, $bookingId) {
            // 1. Deduct stock only once per order
            app(BarOrderStockService::class)->deductForOrder($order, (string) Auth::id());

            // 2. Mark order as charged (NOT settled - will be settled at checkout)
            $order->update([
                'status'         => 'charged',
                'payment_method' => 'charge_to_booking',
                'booking_id'     => $bookingId,
                'billed_to_folio_at' => now(),
            ]);

            // 3. Create BookingCharge — payment will happen at Finance Checkout
            // Store amount in USD (converted from TZS) and also store TZS amount
            $exchangeRate = CurrencyHelper::getExchangeRate();
            $amountUsd = round($order->total / $exchangeRate, 2);

            app(ModuleBillingService::class)->syncOrderCharge($order->fresh(), (string) Auth::id());

            // 4. Free up the table
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

        // Redirect to Finance Checkout page
        return redirect()
            ->route('finance.checkout.show', $bookingId)
            ->with('success', "Order {$order->order_number} charged to booking. Please complete payment at checkout.");
    }

    /**
     * POST /restaurant/orders/{order}/cancel
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->status === 'settled', 422, 'Cannot cancel a settled order.');

        DB::transaction(function () use ($order) {
            if ($order->stock_deducted_at && !$order->stock_reversed_at) {
                app(BarOrderStockService::class)->reverseForCancelledOrder($order, (string) Auth::id());
            }

            app(ModuleBillingService::class)->voidChargeForOrder($order);

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
            'selected_option_value_ids' => 'nullable|array',
            'selected_option_value_ids.*' => 'uuid|exists:menu_option_values,id',
        ]);

        DB::transaction(function () use ($request, $order) {
            $payload = $this->buildOrderItemPayload($order, [
                'menu_item_id' => $request->menu_item_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'selected_option_value_ids' => $request->selected_option_value_ids ?? [],
            ]);

            // If item already in order with same options, increase quantity
            $existing = $order->items()
                ->where('menu_item_id', $payload['menu_item_id'])
                ->where('options_signature', $payload['options_signature'])
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $request->quantity;
                $existing->update([
                    'quantity' => $newQty,
                    'subtotal' => $newQty * $existing->unit_price,
                    'notes'    => $request->notes ?? $existing->notes,
                ]);
            } else {
                OrderItem::create($payload);
            }

            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', __('general.restaurant.messages.order_item_added'));
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

    protected function isBartenderManagedBarOrder(Order $order): bool
    {
        return in_array($order->order_source, ['walkin'])
            && $order->bartender_status !== null;
    }

    protected function buildOrderItemPayload(Order $order, array $item): array
    {
        $menuItem = MenuItem::with([
            'category',
            'optionGroups' => fn($q) => $q->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])->where('is_active', true)->findOrFail($item['menu_item_id']);

        abort_if(!$menuItem->is_available, 422, __('general.restaurant.messages.item_unavailable'));

        $selectedIds = collect($item['selected_option_value_ids'] ?? [])->filter()->unique()->values();
        $selectedValues = MenuOptionValue::with('group')
            ->whereIn('id', $selectedIds)
            ->where('is_active', true)
            ->get();

        $selectedByGroup = $selectedValues->groupBy('menu_option_group_id');
        $allowedValueIds = $menuItem->optionGroups->flatMap(fn($group) => $group->values->pluck('id'))->map(fn($id) => (string) $id)->values();
        $invalidSelections = $selectedIds->diff($allowedValueIds);
        abort_if($invalidSelections->isNotEmpty(), 422, __('general.restaurant.messages.invalid_option_selection'));

        $selectedSnapshots = [];

        foreach ($menuItem->optionGroups as $group) {
            $groupSelections = $selectedByGroup->get($group->id, collect())->values();

            if ($group->is_required && $groupSelections->isEmpty()) {
                abort(422, __('general.restaurant.messages.required_option_missing', ['group' => $group->name]));
            }

            if ($group->selection_type === 'single' && $groupSelections->count() > 1) {
                abort(422, __('general.restaurant.messages.single_option_multiple_selected', ['group' => $group->name]));
            }

            if ($groupSelections->isNotEmpty()) {
                $selectedSnapshots[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'selection_type' => $group->selection_type,
                    'required' => (bool) $group->is_required,
                    'values' => $groupSelections->map(fn($value) => [
                        'id' => $value->id,
                        'label' => $value->label,
                        'price_delta' => (float) $value->price_delta,
                    ])->values()->all(),
                ];
            }
        }

        $optionsUnitPrice = (float) $selectedValues->sum(fn($value) => (float) $value->price_delta);
        $basePrice = (float) $menuItem->selling_price;
        $unitPrice = $basePrice + $optionsUnitPrice;
        $quantity = (int) $item['quantity'];
        $signature = $selectedIds->isEmpty()
            ? 'none'
            : sha1($selectedIds->map(fn($id) => (string) $id)->sort()->implode(','));

        return [
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'item_name_snapshot' => $menuItem->name,
            'quantity' => $quantity,
            'base_unit_price' => $basePrice,
            'options_unit_price' => $optionsUnitPrice,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
            'selected_options_snapshot' => $selectedSnapshots,
            'options_signature' => $signature,
            'notes' => $item['notes'] ?? null,
            'status' => 'pending',
        ];
    }
}
