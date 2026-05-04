<?php
// app/Http/Controllers/ConferenceBookingController.php

namespace App\Http\Controllers;

use App\Models\ConferenceBooking;
use App\Models\ConferenceHall;
use App\Models\Institution;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConferenceBookingController extends Controller
{
    public function index()
    {
        $bookings = ConferenceBooking::with(['conferenceHall', 'institution', 'conference'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('conference-bookings.index', compact('bookings'));
    }

    public function create()
    {
        $halls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();

        return view('conference-bookings.create', compact('halls', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'institution_id'     => 'required|uuid|exists:institutions,id',
            'booking_date'       => 'required|date|after_or_equal:today',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
        ]);

        // Validate availability with 30-minute buffer
        if (!$this->isTimeSlotAvailable(
            $validated['conference_hall_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return back()->withInput()->with('error', 'Time slot not available. Remember the 30-minute cleanup buffer.');
        }

        $hall = ConferenceHall::findOrFail($validated['conference_hall_id']);
        
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $hours = $start->diffInHours($end, true);
        $totalCost = $hours * $hall->hourly_rate;

        $booking = ConferenceBooking::create([
            'conference_hall_id' => $validated['conference_hall_id'],
            'institution_id'     => $validated['institution_id'],
            'booking_date'       => $validated['booking_date'],
            'start_time'         => $validated['start_time'],
            'end_time'           => $validated['end_time'],
            'total_cost'         => $totalCost,
            'status'             => 'pending',
            'created_by'         => auth()->id(),
        ]);

        return redirect()->route('conference-bookings.show', $booking)
            ->with('success', 'Conference booking created successfully.');
    }

    public function show(ConferenceBooking $conferenceBooking)
    {
        $conferenceBooking->load(['conferenceHall', 'institution', 'conference.participants', 'creator']);
        
        return view('conference-bookings.show', compact('conferenceBooking'));
    }

    public function edit(ConferenceBooking $conferenceBooking)
    {
        $halls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();

        return view('conference-bookings.edit', compact('conferenceBooking', 'halls', 'institutions'));
    }

    public function update(Request $request, ConferenceBooking $conferenceBooking)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'institution_id'     => 'required|uuid|exists:institutions,id',
            'booking_date'       => 'required|date',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
            'status'             => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        // Validate availability (excluding current booking)
        if (!$this->isTimeSlotAvailable(
            $validated['conference_hall_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time'],
            $conferenceBooking->id
        )) {
            return back()->withInput()->with('error', 'Time slot not available. Remember the 30-minute cleanup buffer.');
        }

        $hall = ConferenceHall::findOrFail($validated['conference_hall_id']);
        
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $hours = $start->diffInHours($end, true);
        $totalCost = $hours * $hall->hourly_rate;

        $conferenceBooking->update([
            'conference_hall_id' => $validated['conference_hall_id'],
            'institution_id'     => $validated['institution_id'],
            'booking_date'       => $validated['booking_date'],
            'start_time'         => $validated['start_time'],
            'end_time'           => $validated['end_time'],
            'total_cost'         => $totalCost,
            'status'             => $validated['status'],
        ]);

        return redirect()->route('conference-bookings.show', $conferenceBooking)
            ->with('success', 'Conference booking updated successfully.');
    }

    public function destroy(ConferenceBooking $conferenceBooking)
    {
        $conferenceBooking->delete();

        return redirect()->route('conference-bookings.index')
            ->with('success', 'Conference booking deleted successfully.');
    }

    public function confirm(ConferenceBooking $conferenceBooking)
    {
        if ($conferenceBooking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $conferenceBooking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function cancel(ConferenceBooking $conferenceBooking)
    {
        if (!in_array($conferenceBooking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed bookings can be cancelled.');
        }

        $conferenceBooking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    private function isTimeSlotAvailable($hallId, $date, $startTime, $endTime, $excludeBookingId = null)
    {
        $endTimeWithBuffer = Carbon::parse($endTime)->addMinutes(30)->format('H:i:s');

        $conflicts = ConferenceBooking::where('conference_hall_id', $hallId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->where(function ($query) use ($startTime, $endTime, $endTimeWithBuffer) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // New booking starts during existing booking (including buffer)
                    $q->whereRaw("TIME(?) >= start_time", [$startTime])
                      ->whereRaw("TIME(?) < DATE_ADD(end_time, INTERVAL 30 MINUTE)", [$startTime]);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    // New booking ends during existing booking
                    $q->whereRaw("TIME(?) > start_time", [$endTime])
                      ->whereRaw("TIME(?) <= end_time", [$endTime]);
                })
                ->orWhere(function ($q) use ($startTime, $endTimeWithBuffer) {
                    // New booking completely contains existing booking
                    $q->whereRaw("TIME(?) <= start_time", [$startTime])
                      ->whereRaw("TIME(?) >= end_time", [$endTimeWithBuffer]);
                });
            })
            ->exists();

        return !$conflicts;
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'booking_date'       => 'required|date',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i',
        ]);

        $available = $this->isTimeSlotAvailable(
            $validated['conference_hall_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        );

        return response()->json(['available' => $available]);
    }
}
