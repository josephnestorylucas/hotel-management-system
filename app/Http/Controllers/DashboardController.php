<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Reservation;
use App\Models\Building;
use App\Models\User;
use App\Models\LaundryOrder;
use App\Models\BookingCharge;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\InternalUsageRequest;
use App\Models\StoreNotification;
use App\Models\LocalPurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController — role-specific dashboards.
 *
 * Revenue / check-in / check-out stats are computed from Booking (active stays).
 * Upcoming arrivals / pending counts are computed from Reservation (future holds).
 */
class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isAccountant()) {
            return redirect()->route('accountant.dashboard');
        } elseif ($user->isGeneralManager()) {
            return $this->managerDashboard();
        } elseif ($user->isStoreManager()) {
            return $this->storeManagerDashboard();
        } elseif ($user->isSupervisor()) {
            return $this->supervisorDashboard();
        } elseif ($user->isHouseHelp()) {
            return $this->houseHelpDashboard();
        } elseif ($user->isStoreKeeper()) {
            return $this->storeKeeperDashboard();
        } elseif ($user->isRestaurantManager()) {
            return $this->restaurantManagerDashboard();
        } elseif ($user->isBarTender()) {
            return $this->barTenderDashboard();
        } else {
            return $this->frontDeskDashboard();
        }
    }

    private function adminDashboard() {
        $stats = [
            'total_buildings' => Building::count(),
            'total_rooms' => Room::count(),
            'active_rooms' => Room::where('is_active', true)->count(),
            'total_users' => User::where('is_active', true)->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            // Expected arrivals today (Reservation — future holds)
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            // Guests who need to check out today (Booking — active stays)
            'today_checkouts' => Booking::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
            'pending_lpo_approvals' => LocalPurchaseOrder::where('status', 'pending_approval')->count(),
            'supplier_count' => Supplier::where('is_active', true)->count(),
            'low_stock_items' => StockLevel::join('products', 'stock_levels.product_id', '=', 'products.id')
                ->where('products.is_active', true)
                ->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level')
                ->where('stock_levels.quantity', '>', 0)
                ->count(),
            'out_of_stock_items' => StockLevel::join('products', 'stock_levels.product_id', '=', 'products.id')
                ->where('products.is_active', true)
                ->where('stock_levels.quantity', '<=', 0)
                ->count(),
        ];

        $roomStatusCounts = Room::where('is_active', true)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $reservationStatusCounts = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Revenue stats — computed from Booking (active stays + completed stays)
        $stats['today_revenue'] = Booking::whereDate('created_at', today())
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['week_revenue'] = Booking::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Booking::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['total_revenue'] = Booking::whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

        // Recent reservations (future holds)
        $recentReservations = Reservation::with(['room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::with('role')
            ->latest()
            ->limit(5)
            ->get();

        $buildingStats = Building::withCount(['floors', 'rooms'])->get();

        return view('dashboards.admin', compact(
            'stats',
            'roomStatusCounts',
            'reservationStatusCounts',
            'recentReservations',
            'recentUsers',
            'buildingStats'
        ));
    }

    private function managerDashboard() {
        $stats = [
            'total_buildings' => Building::count(),
            'total_rooms' => Room::count(),
            'active_rooms' => Room::where('is_active', true)->count(),
            'total_users' => User::where('is_active', true)->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            'dirty_rooms' => Room::where('status', 'dirty')->count(),
            // Expected arrivals today (Reservation)
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            // Guests who need to check out today (Booking)
            'today_checkouts' => Booking::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
        ];

        // Occupancy rate
        $stats['occupancy_rate'] = $stats['total_rooms'] > 0
            ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1)
            : 0;

        // Revenue stats — computed from Booking
        $stats['today_revenue'] = Booking::whereDate('created_at', today())
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['week_revenue'] = Booking::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Booking::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['total_revenue'] = Booking::whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

        // Pending approvals count (procurement, internal usage requests, etc.)
        $stats['pending_approvals'] = InternalUsageRequest::where('status', 'pending')->count();

        $roomStatusCounts = Room::where('is_active', true)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $reservationStatusCounts = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent reservations
        $recentReservations = Reservation::with(['room', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $buildingStats = Building::withCount(['floors', 'rooms'])->get();

        // Staff by role
        $staffByRole = User::with('role')
            ->where('is_active', true)
            ->get()
            ->groupBy(fn($user) => ucwords(str_replace('_', ' ', $user->role->name ?? 'Unknown')))
            ->map->count();

        // Pending internal usage requests for approval
        $pendingApprovals = InternalUsageRequest::with(['requester', 'location'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        $pendingLpoApprovals = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->where('status', 'pending_approval')
            ->latest('order_date')
            ->limit(6)
            ->get();

        $stockAlerts = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->where(function ($query) {
                $query->where('stock_levels.quantity', '<=', 0)
                    ->orWhereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
            })
            ->select('stock_levels.*')
            ->orderBy('stock_levels.quantity')
            ->limit(8)
            ->get();

        $recentStockMovements = StockMovement::with(['product', 'location', 'actor'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('dashboards.manager', compact(
            'stats',
            'roomStatusCounts',
            'reservationStatusCounts',
            'recentReservations',
            'buildingStats',
            'staffByRole',
            'pendingApprovals',
            'pendingLpoApprovals',
            'stockAlerts',
            'recentStockMovements'
        ));
    }

    private function supervisorDashboard() {
        $stats = [
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'dirty_rooms' => Room::where('status', 'dirty')->count(),
            'out_of_order_rooms' => Room::where('status', 'out_of_order')->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            // Expected arrivals (Reservation)
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            // Guests due to depart (Booking)
            'today_checkouts' => Booking::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // Laundry stats
        $stats['pending_laundry'] = LaundryOrder::where('status', 'pending')->count();
        $stats['inprogress_laundry'] = LaundryOrder::where('status', 'in_progress')->count();
        $stats['completed_laundry'] = LaundryOrder::where('status', 'completed')->count();
        $stats['delivered_laundry'] = LaundryOrder::where('status', 'delivered')->count();
        $stats['today_laundry'] = LaundryOrder::whereDate('created_at', today())->count();

        $occupancyRate = $stats['total_rooms'] > 0 
            ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1) 
            : 0;
        $stats['occupancy_rate'] = $occupancyRate;

        $roomStatusCounts = Room::where('is_active', true)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Today's arrivals (Reservation — pending/confirmed arriving today)
        $todayActivity = Reservation::with('room')
            ->whereDate('check_in_date', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in_date')
            ->get();

        // Today's departures (Booking — checked-in guests due to depart today)
        $todayDepartures = Booking::with('room')
            ->whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->orderBy('check_out_date')
            ->get();

        // Upcoming arrivals (Reservation — future holds)
        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();

        $roomsNeedingAttention = Room::with(['floor.building', 'roomType'])
            ->whereIn('status', ['dirty', 'out_of_order'])
            ->get();

        $recentLaundryOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        // Revenue stats — from Booking
        $stats['today_revenue'] = Booking::whereDate('created_at', today())
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['week_revenue'] = Booking::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Booking::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

        return view('dashboards.supervisor', compact(
            'stats',
            'roomStatusCounts',
            'todayActivity',
            'todayDepartures',
            'upcomingArrivals',
            'roomsNeedingAttention',
            'recentLaundryOrders'
        ));
    }

    private function houseHelpDashboard() {
        $stats = [
            'pending_orders' => LaundryOrder::where('status', 'pending')->count(),
            'inprogress_orders' => LaundryOrder::where('status', 'in_progress')->count(),
            'completed_orders' => LaundryOrder::where('status', 'completed')->count(),
            'delivered_orders' => LaundryOrder::where('status', 'delivered')->count(),
            'today_orders' => LaundryOrder::whereDate('created_at', today())->count(),
            'total_orders' => LaundryOrder::count(),
        ];

        $stats['dirty_rooms'] = Room::where('status', 'dirty')->count();
        $stats['out_of_order_rooms'] = Room::where('status', 'out_of_order')->count();

        $recentOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $ordersByStatus = LaundryOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboards.house-help', compact('stats', 'recentOrders', 'ordersByStatus'));
    }

    private function frontDeskDashboard() {
        $stats = [
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            // All bookings checked in today
            'today_checkins' => Booking::whereDate('check_in_date', today())->count(),
            // All bookings checking out today
            'today_checkouts' => Booking::whereDate('check_out_date', today())->count(),
            // Reservations arriving today (confirmed, pending, converted)
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
        ];

        // Today's check-ins (Bookings)
        $todayActivity = Booking::with('room')
            ->whereDate('check_in_date', today())
            ->orderBy('check_in_date')
            ->get();

        // Today's check-outs (Bookings)
        $todayDepartures = Booking::with('room')
            ->whereDate('check_out_date', today())
            ->orderBy('check_out_date')
            ->get();

        // Expected arrivals from reservations (today + next 3 days)
        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(3)])
            ->whereIn('status', ['pending', 'confirmed', 'converted'])
            ->orderBy('check_in_date')
            ->get();

        // Recent reservations created by this user
        $myRecentReservations = Reservation::with('room')
            ->where('created_by', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        $availableRoomsByType = Room::with('roomType')
            ->where('status', 'available')
            ->where('is_active', true)
            ->select('room_type_id', DB::raw('count(*) as count'))
            ->groupBy('room_type_id')
            ->get();

        return view('dashboards.front-desk', compact(
            'stats',
            'todayActivity',
            'todayDepartures',
            'upcomingArrivals',
            'myRecentReservations',
            'availableRoomsByType'
        ));
    }

    private function storeManagerDashboard() {
        // Procurement-focused statistics
        $stats = [
            // Supplier stats
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            
            // Purchase Order stats
            'total_lpos' => LocalPurchaseOrder::count(),
            'pending_lpos' => LocalPurchaseOrder::where('status', 'pending')->count(),
            'approved_lpos' => LocalPurchaseOrder::where('status', 'approved')->count(),
            'sent_lpos' => LocalPurchaseOrder::where('status', 'sent')->count(),
            
            // GRN stats
            'total_grns' => GoodsReceivedNote::count(),
            'pending_grns' => GoodsReceivedNote::whereIn('status', [
                GoodsReceivedNote::STATUS_SUBMITTED,
                GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
            ])->count(),
            'confirmed_grns' => GoodsReceivedNote::where('status', GoodsReceivedNote::STATUS_APPROVED)->count(),
            
            // Product & Stock stats
            'total_products' => Product::count(),
            'low_stock_items' => StockLevel::whereColumn('quantity', '<=', 'reserved_qty')->count(),
            
            // Today's activity
            'today_lpos' => LocalPurchaseOrder::whereDate('created_at', today())->count(),
            'today_grns' => GoodsReceivedNote::whereDate('created_at', today())->count(),
            
            // Financial summary (procurement spending)
            'month_spending' => GoodsReceivedNote::whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)
                ->where('status', GoodsReceivedNote::STATUS_APPROVED)
                ->sum('grand_total'),
            'pending_orders_value' => LocalPurchaseOrder::whereIn('status', ['pending', 'approved', 'sent'])
                ->sum('grand_total'),
        ];

        // LPO status distribution
        $lpoStatusCounts = LocalPurchaseOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // GRN status distribution
        $grnStatusCounts = GoodsReceivedNote::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent Purchase Orders
        $recentLpos = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Recent Goods Received Notes
        $recentGrns = GoodsReceivedNote::with(['supplier', 'lpo', 'receiver'])
            ->latest()
            ->limit(10)
            ->get();

        // Pending approvals (LPOs awaiting approval)
        $pendingApprovals = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Pending GRN confirmations
        $pendingGrnConfirmations = GoodsReceivedNote::with(['supplier', 'lpo', 'receiver'])
            ->whereIn('status', [
                GoodsReceivedNote::STATUS_SUBMITTED,
                GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
            ])
            ->latest()
            ->limit(10)
            ->get();

        // Top suppliers (by order count this month)
        $topSuppliers = Supplier::withCount(['purchaseOrders' => function ($query) {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }])
            ->where('is_active', true)
            ->orderByDesc('purchase_orders_count')
            ->limit(5)
            ->get();

        // Low stock products needing reorder
        $lowStockProducts = Product::with(['stockLevels'])
            ->whereHas('stockLevels', function ($query) {
                $query->whereColumn('quantity', '<=', 'reserved_qty');
            })
            ->limit(10)
            ->get();

        return view('dashboards.store-manager', compact(
            'stats',
            'lpoStatusCounts',
            'grnStatusCounts',
            'recentLpos',
            'recentGrns',
            'pendingApprovals',
            'pendingGrnConfirmations',
            'topSuppliers',
            'lowStockProducts'
        ));
    }

    private function storeKeeperDashboard() {
        $stats = [
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_items' => StockLevel::query()
                ->whereHas('product', function ($query) {
                    $query->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
                })
                ->count(),
            'pending_internal_requests' => InternalUsageRequest::whereIn('status', ['pending', 'approved'])->count(),
            'pending_transfers' => StockTransfer::where('status', 'pending')->count(),
        ];

        $lowStockProducts = StockLevel::with(['product', 'location'])
            ->whereHas('product', function ($query) {
                $query->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
            })
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $pendingInternalRequests = InternalUsageRequest::with(['product', 'requester'])
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTransfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.store-keeper', compact(
            'stats',
            'lowStockProducts',
            'pendingInternalRequests',
            'recentTransfers'
        ));
    }

    private function restaurantManagerDashboard() {
        $barLocation     = \App\Models\StockLocation::bar();
        $kitchenLocation = \App\Models\StockLocation::kitchen();

        $locationIds = collect([$barLocation, $kitchenLocation])->filter()->pluck('id');

        $stats = [
            'total_bar_products'     => $barLocation ? StockLevel::where('location_id', $barLocation->id)->where('quantity', '>', 0)->count() : 0,
            'total_kitchen_products' => $kitchenLocation ? StockLevel::where('location_id', $kitchenLocation->id)->where('quantity', '>', 0)->count() : 0,
            'low_stock_items'        => 0,
            'pending_transfers'      => StockTransfer::where('status', 'pending')
                ->whereIn('to_location_id', $locationIds)
                ->count(),
            'today_movements'        => StockMovement::whereDate('created_at', today())
                ->whereIn('location_id', $locationIds)
                ->count(),
        ];

        // Low stock across both locations
        foreach ([$barLocation, $kitchenLocation] as $loc) {
            if ($loc) {
                $stats['low_stock_items'] += StockLevel::where('location_id', $loc->id)
                    ->whereColumn('quantity', '<=', 'reserved_qty')
                    ->orWhere(function ($q) use ($loc) {
                        $q->where('location_id', $loc->id)
                          ->whereHas('product', function ($pq) {
                              $pq->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
                          });
                    })->count();
            }
        }

        $recentMovements = StockMovement::with(['product', 'location', 'user'])
            ->whereIn('location_id', $locationIds)
            ->latest()
            ->limit(10)
            ->get();

        $notifications = StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.restaurant-manager', compact('stats', 'recentMovements', 'notifications'));
    }

    private function barTenderDashboard() {
        $barLocation = \App\Models\StockLocation::bar();
        $stats = [
            'available_items' => $barLocation ? StockLevel::where('location_id', $barLocation->id)->where('quantity', '>', 0)->count() : 0,
            'today_served' => StockMovement::whereDate('created_at', today())
                ->where('type', 'internal_use')
                ->when($barLocation, fn($q) => $q->where('location_id', $barLocation->id))
                ->count(),
        ];

        $stockLevels = StockLevel::with('product')
            ->when($barLocation, fn($q) => $q->where('location_id', $barLocation->id))
            ->orderBy('quantity', 'asc')
            ->limit(20)
            ->get();

        return view('dashboards.bar-tender', compact('stats', 'stockLevels'));
    }

}
