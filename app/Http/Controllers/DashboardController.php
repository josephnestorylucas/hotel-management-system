<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Building;
use App\Models\User;
use App\Models\LaundryTask;
use App\Models\LaundryOrder;
use App\Models\BookingCharge;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        
        // Route to role-specific dashboard
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        } elseif ($user->isSupervisor()) {
            return $this->supervisorDashboard();
        } elseif ($user->isHouseHelp()) {
            return $this->houseHelpDashboard();
        } elseif ($user->isStoreKeeper()) {
            return $this->storeKeeperDashboard();
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
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            'today_checkouts' => Reservation::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
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

        // Revenue stats
        $stats['today_revenue'] = Reservation::whereDate('check_in_date', today())
            ->where('status', 'checked_in')->sum('total_amount');
        $stats['week_revenue'] = Reservation::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Reservation::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['total_revenue'] = Reservation::whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

        // Recent activities
        $recentReservations = Reservation::with(['room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::with('role')
            ->latest()
            ->limit(5)
            ->get();

        // Building stats
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

    private function supervisorDashboard() {
        $stats = [
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'dirty_rooms' => Room::where('status', 'dirty')->count(),
            'out_of_order_rooms' => Room::where('status', 'out_of_order')->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            'today_checkouts' => Reservation::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // Laundry stats (new system)
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

        // Today's activity
        $todayActivity = Reservation::with('room')
            ->where(function ($query) {
                $query->whereDate('check_in_date', today())
                      ->orWhereDate('check_out_date', today());
            })
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->orderBy('check_in_date')
            ->get();

        // Upcoming arrivals
        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();

        // Rooms requiring attention
        $roomsNeedingAttention = Room::with(['floor.building', 'roomType'])
            ->whereIn('status', ['dirty', 'out_of_order'])
            ->get();

        // Recent laundry orders (new system)
        $recentLaundryOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        // Revenue stats
        $stats['today_revenue'] = Reservation::whereDate('check_in_date', today())
            ->where('status', 'checked_in')->sum('total_amount');
        $stats['week_revenue'] = Reservation::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Reservation::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

        return view('dashboards.supervisor', compact(
            'stats',
            'roomStatusCounts',
            'todayActivity',
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

        // Room stats for house help
        $stats['dirty_rooms'] = Room::where('status', 'dirty')->count();
        $stats['out_of_order_rooms'] = Room::where('status', 'out_of_order')->count();

        // Recent laundry orders
        $recentOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Orders by status
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
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            'today_checkouts' => Reservation::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // Today's activity
        $todayActivity = Reservation::with('room')
            ->where(function ($query) {
                $query->whereDate('check_in_date', today())
                      ->orWhereDate('check_out_date', today());
            })
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->orderBy('check_in_date')
            ->get();

        // Upcoming arrivals (next 3 days)
        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(3)])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('check_in_date')
            ->get();

        // Recent reservations created by this user
        $myRecentReservations = Reservation::with('room')
            ->where('created_by', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        // Available rooms by type
        $availableRoomsByType = Room::with('roomType')
            ->where('status', 'available')
            ->where('is_active', true)
            ->select('room_type_id', DB::raw('count(*) as count'))
            ->groupBy('room_type_id')
            ->get();

        return view('dashboards.front-desk', compact(
            'stats',
            'todayActivity',
            'upcomingArrivals',
            'myRecentReservations',
            'availableRoomsByType'
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
            'out_of_order_rooms' => Room::where('status', 'out_of_order')->count(),
            'today_checkins' => Reservation::whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'pending'])->count(),
            'today_checkouts' => Reservation::whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // Occupancy rate
        $stats['occupancy_rate'] = $stats['total_rooms'] > 0
            ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1)
            : 0;

        // Revenue stats
        $stats['today_revenue'] = Reservation::whereDate('check_in_date', today())
            ->where('status', 'checked_in')->sum('total_amount');
        $stats['week_revenue'] = Reservation::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['month_revenue'] = Reservation::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');
        $stats['total_revenue'] = Reservation::whereIn('status', ['checked_in', 'checked_out'])->sum('total_amount');

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

        // Building stats
        $buildingStats = Building::withCount(['floors', 'rooms'])->get();

        // Staff overview
        $staffByRole = User::with('role')
            ->where('is_active', true)
            ->get()
            ->groupBy(fn($user) => $user->role->display_name ?? 'Unknown')
            ->map->count();

        return view('dashboards.manager', compact(
            'stats',
            'roomStatusCounts',
            'reservationStatusCounts',
            'recentReservations',
            'buildingStats',
            'staffByRole'
        ));
    }

    private function storeKeeperDashboard() {
        $stats = [
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'dirty_rooms' => Room::where('status', 'dirty')->count(),
            'out_of_order_rooms' => Room::where('status', 'out_of_order')->count(),
        ];

        // Laundry stats (new system)
        $stats['pending_laundry'] = LaundryOrder::where('status', 'pending')->count();
        $stats['inprogress_laundry'] = LaundryOrder::where('status', 'in_progress')->count();
        $stats['completed_laundry'] = LaundryOrder::where('status', 'completed')->count();
        $stats['delivered_laundry'] = LaundryOrder::where('status', 'delivered')->count();
        $stats['today_laundry'] = LaundryOrder::whereDate('created_at', today())->count();

        // Laundry orders by status
        $laundryStatusCounts = LaundryOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent laundry orders
        $recentLaundryOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Rooms needing attention
        $roomsNeedingAttention = Room::with(['floor.building', 'roomType'])
            ->whereIn('status', ['dirty', 'out_of_order'])
            ->get();

        return view('dashboards.store-keeper', compact(
            'stats',
            'laundryStatusCounts',
            'recentLaundryOrders',
            'roomsNeedingAttention'
        ));
    }
}