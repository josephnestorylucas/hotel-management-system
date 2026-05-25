<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function dashboard(Organization $organization, Event $event)
    {
        $event->load(['attendances', 'schedules']);

        $stats = [
            'total_registered' => $event->attendances()->where('registration_status', 'confirmed')->count(),
            'checked_in' => $event->attendances()->where('total_check_ins', '>', 0)->count(),
            'not_checked_in' => $event->attendances()
                ->where('registration_status', 'confirmed')
                ->where('total_check_ins', 0)
                ->count(),
            'no_shows' => $event->attendances()->where('registration_status', 'no_show')->count(),
        ];

        $recentCheckIns = CheckIn::whereHas('attendance', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->with(['attendance', 'eventSchedule', 'staff'])
            ->latest()
            ->limit(20)
            ->get();

        $scheduleCheckIns = $event->schedules->map(function ($schedule) use ($event) {
            return [
                'schedule' => $schedule,
                'count' => CheckIn::where('event_schedule_id', $schedule->id)->count(),
            ];
        });

        return view('check-ins.dashboard', compact('organization', 'event', 'stats', 'recentCheckIns', 'scheduleCheckIns'));
    }

    public function scanner(Organization $organization, Event $event)
    {
        $schedules = $event->schedules()->orderBy('start_datetime')->get();
        return view('check-ins.scanner', compact('organization', 'event', 'schedules'));
    }

    public function process(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
            'event_schedule_id' => 'nullable|uuid|exists:event_schedules,id',
        ]);

        $attendance = Attendance::where('qr_token', $validated['qr_token'])
            ->where('event_id', $event->id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code.',
            ], 404);
        }

        return $this->performCheckIn($attendance, $event, $validated['event_schedule_id'] ?? null, 'qr_scan');
    }

    public function manualEntry(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'manual_code' => 'required|string|max:8',
            'event_schedule_id' => 'nullable|uuid|exists:event_schedules,id',
        ]);

        $attendance = Attendance::where('manual_code', strtoupper($validated['manual_code']))
            ->where('event_id', $event->id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid manual code.',
            ], 404);
        }

        return $this->performCheckIn($attendance, $event, $validated['event_schedule_id'] ?? null, 'manual_code');
    }

    public function staffOverride(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'attendance_id' => 'required|uuid|exists:attendances,id',
            'event_schedule_id' => 'nullable|uuid|exists:event_schedules,id',
            'notes' => 'nullable|string',
        ]);

        $attendance = Attendance::findOrFail($validated['attendance_id']);

        if ($attendance->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Attendance does not belong to this event.'], 400);
        }

        $checkIn = $attendance->checkIn(
            $validated['event_schedule_id'] ?? null,
            'staff_override',
            auth()->id()
        );

        if (isset($validated['notes'])) {
            $checkIn->update(['verification_notes' => $validated['notes']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in recorded (staff override).',
            'data' => [
                'name' => $attendance->full_name,
                'check_in_count' => $attendance->total_check_ins,
                'time' => now()->format('h:i A'),
            ],
        ]);
    }

    private function performCheckIn(Attendance $attendance, Event $event, ?string $scheduleId, string $method)
    {
        if (!$event->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Event is not active.',
            ], 400);
        }

        if ($attendance->registration_status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Attendee registration is not confirmed. Status: ' . $attendance->registration_status,
            ], 400);
        }

        $checkIn = $attendance->checkIn($scheduleId, $method, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => [
                'name' => $attendance->full_name,
                'company' => $attendance->company,
                'pass_tier' => $attendance->eventPass?->tier_name,
                'pass_type' => $attendance->pass_type,
                'check_in_count' => $attendance->total_check_ins,
                'time' => now()->format('h:i A'),
            ],
        ]);
    }

    // API endpoints for mobile scanner
    public function apiProcess(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
            'event_id' => 'required|uuid|exists:events,id',
        ]);

        $attendance = Attendance::where('qr_token', $validated['qr_token'])
            ->where('event_id', $validated['event_id'])
            ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Invalid QR code.'], 404);
        }

        $event = Event::find($validated['event_id']);

        return $this->performCheckIn($attendance, $event, null, 'qr_scan');
    }

    public function apiManual(Request $request)
    {
        $validated = $request->validate([
            'manual_code' => 'required|string|max:8',
            'event_id' => 'required|uuid|exists:events,id',
        ]);

        $attendance = Attendance::where('manual_code', strtoupper($validated['manual_code']))
            ->where('event_id', $validated['event_id'])
            ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Invalid manual code.'], 404);
        }

        $event = Event::find($validated['event_id']);

        return $this->performCheckIn($attendance, $event, null, 'manual_code');
    }
}
