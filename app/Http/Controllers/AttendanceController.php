<?php

namespace App\Http\Controllers;

use App\Mail\AttendeeTicketMail;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventPass;
use App\Models\Guest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Organization $organization, Event $event)
    {
        $query = $event->attendances()->with('eventPass');

        if ($status = request('status')) {
            $query->where('registration_status', $status);
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhere('manual_code', 'like', "%{$search}%");
            });
        }

        if (request()->wantsJson()) {
            $attendances = $query->limit(10)->get();
            return response()->json([
                'data' => $attendances->map(fn($a) => [
                    'id' => $a->id,
                    'first_name' => $a->first_name,
                    'last_name' => $a->last_name,
                    'email' => $a->email,
                    'manual_code' => $a->manual_code,
                    'pass_type' => $a->pass_type,
                    'registration_status' => $a->registration_status,
                ]),
            ]);
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => $event->attendances()->count(),
            'confirmed' => $event->attendances()->where('registration_status', 'confirmed')->count(),
            'pending' => $event->attendances()->where('registration_status', 'pending')->count(),
            'checked_in' => $event->attendances()->where('total_check_ins', '>', 0)->count(),
            'no_shows' => $event->attendances()->where('registration_status', 'no_show')->count(),
        ];

        return view('attendances.index', compact('organization', 'event', 'attendances', 'stats'));
    }

    public function create(Organization $organization, Event $event)
    {
        $passes = $event->passes()->get();
        $event->load('schedules');
        return view('attendances.create', compact('organization', 'event', 'passes'));
    }

    public function store(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'event_pass_id' => 'nullable|uuid|exists:event_passes,id',
            'pass_type' => 'nullable|in:speaker,moderator,backdoor,attendee',
            'guest_id' => 'nullable|uuid|exists:guests,id',
            'dietary_requirements' => 'nullable|string|max:255',
            'special_accommodations' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'registration_type' => 'nullable|in:individual,walked_in,complimentary',
        ]);

        // Check for duplicate email in same event
        $exists = $event->attendances()->where('email', $validated['email'])->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'An attendee with this email already exists for this event.');
        }

        $validated['event_id'] = $event->id;
        $validated['registration_status'] = 'confirmed';
        $validated['registration_type'] = $validated['registration_type'] ?? 'individual';

        if (!empty($validated['event_pass_id'])) {
            $pass = EventPass::find($validated['event_pass_id']);
            $validated['pass_type'] = $pass->tier_type ?? 'attendee';
            $pass?->recordRegistration();
        } else {
            $validated['pass_type'] = 'attendee';
        }

        $attendance = Attendance::create($validated);

        // Try to link to existing guest by email
        if (empty($validated['guest_id'])) {
            $guest = Guest::where('email', $validated['email'])->first();
            if ($guest) {
                $attendance->update(['guest_id' => $guest->id]);
            }
        }

        return redirect()->route('organizations.events.attendances.show', [$organization, $event, $attendance])
            ->with('success', 'Attendee registered successfully.');
    }

    public function show(Organization $organization, Event $event, Attendance $attendance)
    {
        $attendance->load(['eventPass', 'guest', 'checkIns.eventSchedule']);
        return view('attendances.show', compact('organization', 'event', 'attendance'));
    }

    public function edit(Organization $organization, Event $event, Attendance $attendance)
    {
        $passes = $event->passes()->get();
        $event->load('schedules');
        return view('attendances.edit', compact('organization', 'event', 'attendance', 'passes'));
    }

    public function update(Request $request, Organization $organization, Event $event, Attendance $attendance)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'event_pass_id' => 'nullable|uuid|exists:event_passes,id',
            'pass_type' => 'nullable|in:speaker,moderator,backdoor,attendee',
            'dietary_requirements' => 'nullable|string|max:255',
            'special_accommodations' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'registration_status' => 'nullable|in:pending,confirmed,cancelled,no_show',
        ]);

        $attendance->update($validated);

        return redirect()->route('organizations.events.attendances.show', [$organization, $event, $attendance])
            ->with('success', 'Attendee updated successfully.');
    }

    public function destroy(Organization $organization, Event $event, Attendance $attendance)
    {
        $this->softDelete($attendance);

        return back()->with('success', 'Attendee deleted.');
    }

    public function bulkUpload(Organization $organization, Event $event)
    {
        return view('attendances.bulk_upload', compact('organization', 'event'));
    }

    public function processBulkUpload(Request $request, Organization $organization, Event $event)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'event_pass_id' => 'nullable|uuid|exists:event_passes,id',
            'pass_type' => 'nullable|in:speaker,moderator,backdoor,attendee',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        $lineNumber = 1;

        $ticketId = $request->input('event_pass_id');

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;
            $data = array_combine($header, $row);

            if (empty($data['email']) || empty($data['first_name']) || empty($data['last_name'])) {
                $results['errors'][] = "Line {$lineNumber}: Missing required fields (email, first_name, last_name)";
                $results['failed']++;
                continue;
            }

            // Check duplicate
            if ($event->attendances()->where('email', $data['email'])->exists()) {
                $results['errors'][] = "Line {$lineNumber}: Email {$data['email']} already registered";
                $results['failed']++;
                continue;
            }

            try {
                $attendance = Attendance::create([
                    'event_id' => $event->id,
                    'event_pass_id' => $ticketId,
                    'pass_type' => 'attendee',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'company' => $data['company'] ?? null,
                    'job_title' => $data['job_title'] ?? null,
                    'dietary_requirements' => $data['dietary'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'registration_type' => 'bulk_registered',
                    'registration_status' => 'confirmed',
                ]);

                // Try linking to guest
                $guest = Guest::where('email', $data['email'])->first();
                if ($guest) {
                    $attendance->update(['guest_id' => $guest->id]);
                }

                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Line {$lineNumber}: " . $e->getMessage();
                $results['failed']++;
            }
        }

        fclose($handle);

        if ($ticketId && $results['success'] > 0) {
            $pass = EventPass::find($ticketId);
            if ($pass) {
                $pass->increment('quantity_sold', $results['success']);
            }
        }

        return back()->with('bulk_results', $results);
    }

    public function linkGuest(Request $request, Organization $organization, Event $event, Attendance $attendance)
    {
        $validated = $request->validate([
            'guest_id' => 'required|uuid|exists:guests,id',
        ]);

        $attendance->linkToGuest($validated['guest_id']);

        return back()->with('success', 'Attendee linked to guest successfully.');
    }

    public function ticketPdf(Organization $organization, Event $event, Attendance $attendance)
    {
        $attendance->load('event');
        return view('attendances.ticket', compact('organization', 'event', 'attendance'));
    }

    public function sendTicket(Organization $organization, Event $event, Attendance $attendance)
    {
        if (empty($attendance->email)) {
            return back()->with('error', 'Attendee has no email address.');
        }

        Mail::to($attendance->email)->send(new AttendeeTicketMail($attendance));

        return back()->with('success', 'Ticket sent to ' . $attendance->email);
    }

    public function printBadges(Request $request, Organization $organization, Event $event)
    {
        $query = $event->attendances()->where('registration_status', 'confirmed');

        if ($request->has('ids')) {
            $query->whereIn('id', $request->input('ids'));
        }

        $attendances = $query->get();

        return view('attendances.badges', compact('organization', 'event', 'attendances'));
    }

    public function markNoShow(Organization $organization, Event $event, Attendance $attendance)
    {
        $attendance->markAsNoShow();
        return back()->with('success', 'Attendee marked as no-show.');
    }

    public function confirm(Organization $organization, Event $event, Attendance $attendance)
    {
        $attendance->update(['registration_status' => 'confirmed']);
        return back()->with('success', 'Attendee confirmed.');
    }
}
