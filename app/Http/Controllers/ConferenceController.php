<?php
// app/Http/Controllers/ConferenceController.php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceBooking;
use App\Models\ConferenceHall;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConferenceController extends Controller
{
    public function index()
    {
        $conferences = Conference::with(['conferenceBooking.conferenceHall', 'institution'])
            ->withCount('participants')
            ->orderBy('start_datetime', 'desc')
            ->paginate(20);

        return view('conferences.index', compact('conferences'));
    }

    public function create()
    {
        $halls = ConferenceHall::where('status', 'available')
            ->orderBy('name')
            ->get();

        return view('conferences.create', compact('halls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'booking_date'       => 'required|date|after_or_equal:today',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
        ]);

        // Check hall availability with 30-minute buffer
        if (!$this->isTimeSlotAvailable(
            $validated['conference_hall_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return back()->withInput()->withErrors([
                'conference_hall_id' => 'This hall is not available for the selected time slot (including 30-min cleanup buffer).'
            ]);
        }

        $hall = ConferenceHall::findOrFail($validated['conference_hall_id']);
        
        // Calculate cost
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $hours = $start->diffInHours($end, true);
        $totalCost = round($hours * $hall->hourly_rate, 2);

        // Create the booking first
        $booking = ConferenceBooking::create([
            'conference_hall_id' => $validated['conference_hall_id'],
            'booking_date'       => $validated['booking_date'],
            'start_time'         => $validated['start_time'],
            'end_time'           => $validated['end_time'],
            'total_cost'         => $totalCost,
            'status'             => 'confirmed',
            'created_by'         => auth()->id(),
        ]);

        // Create conference linked to booking
        $conference = Conference::create([
            'conference_booking_id' => $booking->id,
            'title'                 => $validated['title'],
            'description'           => $validated['description'],
            'start_datetime'        => $validated['booking_date'] . ' ' . $validated['start_time'],
            'end_datetime'          => $validated['booking_date'] . ' ' . $validated['end_time'],
            'status'                => 'draft',
        ]);

        return redirect()->route('conferences.show', $conference)
            ->with('success', 'Conference created and hall booked successfully.');
    }

    private function isTimeSlotAvailable($hallId, $date, $startTime, $endTime)
    {
        $endTimeWithBuffer = Carbon::parse($endTime)->addMinutes(30)->format('H:i:s');

        $conflicts = ConferenceBooking::where('conference_hall_id', $hallId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
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

    public function show(Conference $conference)
    {
        $conference->load([
            'conferenceBooking.conferenceHall',
            'conferenceBooking.institution',
            'institution',
            'participants.guest'
        ]);

        return view('conferences.show', compact('conference'));
    }

    public function edit(Conference $conference)
    {
        return view('conferences.edit', compact('conference'));
    }

    public function update(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
            'status'         => 'required|in:draft,scheduled,ongoing,completed,cancelled',
        ]);

        $conference->update($validated);

        return redirect()->route('conferences.show', $conference)
            ->with('success', 'Conference updated successfully.');
    }

    public function destroy(Conference $conference)
    {
        $conference->delete();

        return redirect()->route('conferences.index')
            ->with('success', 'Conference deleted successfully.');
    }
}
