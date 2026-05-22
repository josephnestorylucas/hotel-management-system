<?php
// app/Http/Controllers/ConferenceController.php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceBooking;
use App\Models\ConferenceHall;
use Illuminate\Http\Request;

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
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'conference_fee'     => 'required|numeric|min:0',
            'hall_name'          => 'nullable|string|max:255',
            'conference_hall_id' => 'nullable|uuid|exists:conference_halls,id',
            'booking_date'       => 'required_if:conference_hall_id|nullable|date|after_or_equal:today',
            'start_time'         => 'required_if:conference_hall_id|nullable|date_format:H:i',
            'end_time'           => 'required_if:conference_hall_id|nullable|date_format:H:i|after:start_time',
            'institution_id'     => 'nullable|uuid|exists:institutions,id',
            'start_datetime'     => 'required|date',
            'end_datetime'       => 'required|date|after:start_datetime',
        ]);

        $conference = \DB::transaction(function () use ($validated) {
            $conferenceData = [
                'title'          => $validated['title'],
                'description'    => $validated['description'] ?? null,
                'conference_fee' => $validated['conference_fee'],
                'hall_name'      => $validated['hall_name'] ?? null,
                'institution_id' => $validated['institution_id'] ?? null,
                'start_datetime' => $validated['start_datetime'],
                'end_datetime'   => $validated['end_datetime'],
                'status'         => 'draft',
            ];

            if (!empty($validated['conference_hall_id'])) {
                $hall = ConferenceHall::findOrFail($validated['conference_hall_id']);
                $start = \Carbon\Carbon::parse($validated['start_time']);
                $end = \Carbon\Carbon::parse($validated['end_time']);
                $hours = $start->diffInHours($end, true);
                $totalCost = round($hours * $hall->hourly_rate, 2);

                $booking = ConferenceBooking::create([
                    'conference_hall_id' => $validated['conference_hall_id'],
                    'booking_date'       => $validated['booking_date'],
                    'start_time'         => $validated['start_time'],
                    'end_time'           => $validated['end_time'],
                    'total_cost'         => $totalCost,
                    'status'             => 'confirmed',
                    'created_by'         => auth()->id(),
                ]);

                $conferenceData['conference_booking_id'] = $booking->id;
                $conferenceData['hall_name'] = $conferenceData['hall_name'] ?? $hall->name;
            }

            return Conference::create($conferenceData);
        });

        return redirect()->route('conferences.show', $conference)
            ->with('success', 'Conference created successfully.');
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
            'conference_fee' => 'required|numeric|min:0',
            'hall_name'      => 'nullable|string|max:255',
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
