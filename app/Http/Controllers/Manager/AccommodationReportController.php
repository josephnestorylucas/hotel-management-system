<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)
            : now()->startOfWeek();
        $dateTo = $request->date_to
            ? Carbon::parse($request->date_to)
            : now();

        $roomNumber = $request->input('room_number');

        // ── Base query: bookings overlapping the selected date range ──
        $bookingsQuery = Booking::with(['room.roomType', 'guest'])
            ->where('check_in_date', '<=', $dateTo->toDateString())
            ->where('check_out_date', '>=', $dateFrom->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->orderBy('check_in_date', 'desc');

        // Filter by room number if provided
        if ($roomNumber) {
            $bookingsQuery->whereHas('room', function ($q) use ($roomNumber) {
                $q->where('room_number', 'like', "%{$roomNumber}%");
            });
        }

        $bookings = $bookingsQuery->get();

        // ── Summary stats ──
        $totalGuests = $bookings->sum('number_of_guests');
        $totalBookings = $bookings->count();

        // Check-ins per day (within the date range)
        $checkinsByDay = Booking::whereDate('check_in_date', '>=', $dateFrom->toDateString())
            ->whereDate('check_in_date', '<=', $dateTo->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($roomNumber, fn ($q) => $q->whereHas('room', function ($rq) use ($roomNumber) {
                $rq->where('room_number', 'like', "%{$roomNumber}%");
            }))
            ->selectRaw('check_in_date, COUNT(*) as count')
            ->groupBy('check_in_date')
            ->orderBy('check_in_date')
            ->pluck('count', 'check_in_date');

        // Check-outs per day (within the date range)
        $checkoutsByDay = Booking::whereDate('check_out_date', '>=', $dateFrom->toDateString())
            ->whereDate('check_out_date', '<=', $dateTo->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($roomNumber, fn ($q) => $q->whereHas('room', function ($rq) use ($roomNumber) {
                $rq->where('room_number', 'like', "%{$roomNumber}%");
            }))
            ->selectRaw('check_out_date, COUNT(*) as count')
            ->groupBy('check_out_date')
            ->orderBy('check_out_date')
            ->pluck('count', 'check_out_date');

        // Build daily breakdown for the date range
        $dailyBreakdown = collect();
        $period = Carbon::parse($dateFrom);
        while ($period->lte($dateTo)) {
            $dateStr = $period->toDateString();
            $dailyBreakdown->push([
                'date' => $period->copy(),
                'checkins' => $checkinsByDay->get($dateStr, 0),
                'checkouts' => $checkoutsByDay->get($dateStr, 0),
            ]);
            $period->addDay();
        }

        // ── Occupancy calculation ──
        $totalRooms = Room::where('is_active', true)->count();

        // Occupied rooms = rooms with at least one overlapping booking on "today"
        $occupiedToday = Booking::where('check_in_date', '<=', now()->toDateString())
            ->where('check_out_date', '>', now()->toDateString())
            ->where('status', 'checked_in')
            ->distinct('room_id')
            ->count('room_id');

        $occupancyPercent = $totalRooms > 0
            ? round(($occupiedToday / $totalRooms) * 100, 1)
            : 0;

        // Today's check-in / check-out counts
        $todayCheckins = Booking::whereDate('check_in_date', today())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        $todayCheckouts = Booking::whereDate('check_out_date', today())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        return view('manager.reports.accommodation', compact(
            'bookings',
            'totalGuests',
            'totalBookings',
            'dailyBreakdown',
            'totalRooms',
            'occupiedToday',
            'occupancyPercent',
            'todayCheckins',
            'todayCheckouts',
            'dateFrom',
            'dateTo',
            'roomNumber',
        ));
    }
}
