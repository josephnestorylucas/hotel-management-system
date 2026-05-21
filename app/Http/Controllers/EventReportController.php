<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\AttendanceMetrics;
use App\Models\CheckIn;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function preEvent(Organization $organization, Event $event)
    {
        $totalRegistrations = $event->attendances()->count();
        $byStatus = $event->attendances()
            ->selectRaw('registration_status, COUNT(*) as count')
            ->groupBy('registration_status')
            ->pluck('count', 'registration_status');

        $byTicketTier = $event->attendances()
            ->join('event_tickets', 'attendances.event_ticket_id', '=', 'event_tickets.id')
            ->selectRaw('event_tickets.tier_name, COUNT(*) as count')
            ->groupBy('event_tickets.tier_name')
            ->pluck('count', 'tier_name');

        $byCompany = $event->attendances()
            ->whereNotNull('company')
            ->selectRaw('company, COUNT(*) as count')
            ->groupBy('company')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'company');

        $registrationTrend = $event->attendances()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenue = $event->attendances()
            ->where('registration_status', 'confirmed')
            ->whereNotNull('event_ticket_id')
            ->join('event_tickets', 'attendances.event_ticket_id', '=', 'event_tickets.id')
            ->sum('event_tickets.price');

        return view('reports.pre-event', compact(
            'organization', 'event', 'totalRegistrations', 'byStatus',
            'byTicketTier', 'byCompany', 'registrationTrend', 'revenue'
        ));
    }

    public function live(Organization $organization, Event $event)
    {
        $totalRegistered = $event->attendances()->where('registration_status', 'confirmed')->count();
        $checkedIn = $event->attendances()->where('total_check_ins', '>', 0)->count();
        $notCheckedIn = $totalRegistered - $checkedIn;
        $checkInRate = $totalRegistered > 0 ? round(($checkedIn / $totalRegistered) * 100, 1) : 0;

        $checkInTimeline = CheckIn::whereHas('attendance', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $sessionAttendance = $event->schedules->map(function ($schedule) {
            return [
                'session' => $schedule,
                'check_ins' => CheckIn::where('event_schedule_id', $schedule->id)->count(),
            ];
        });

        $recentCheckIns = CheckIn::whereHas('attendance', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->with('attendance')
            ->latest()
            ->limit(10)
            ->get();

        return view('reports.live', compact(
            'organization', 'event', 'totalRegistered', 'checkedIn',
            'notCheckedIn', 'checkInRate', 'checkInTimeline',
            'sessionAttendance', 'recentCheckIns'
        ));
    }

    public function postEvent(Organization $organization, Event $event)
    {
        $totalRegistered = $event->attendances()->count();
        $confirmed = $event->attendances()->where('registration_status', 'confirmed')->count();
        $checkedIn = $event->attendances()->where('total_check_ins', '>', 0)->count();
        $noShows = $event->attendances()->where('registration_status', 'no_show')->count();
        $cancelled = $event->attendances()->where('registration_status', 'cancelled')->count();

        $attendanceRate = $confirmed > 0 ? round(($checkedIn / $confirmed) * 100, 1) : 0;
        $noShowRate = $confirmed > 0 ? round(($noShows / $confirmed) * 100, 1) : 0;

        $byCompany = $event->attendances()
            ->where('registration_status', 'confirmed')
            ->whereNotNull('company')
            ->selectRaw('company, COUNT(*) as count')
            ->groupBy('company')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        $byTicketTier = $event->attendances()
            ->join('event_tickets', 'attendances.event_ticket_id', '=', 'event_tickets.id')
            ->selectRaw('event_tickets.tier_name, event_tickets.price, COUNT(*) as count')
            ->groupBy('event_tickets.tier_name', 'event_tickets.price')
            ->get();

        $revenue = $byTicketTier->sum(fn($t) => $t->price * $t->count);

        $sessionBreakdown = $event->schedules->map(function ($schedule) {
            $checkIns = CheckIn::where('event_schedule_id', $schedule->id)->count();
            return [
                'session' => $schedule,
                'check_ins' => $checkIns,
            ];
        });

        return view('reports.post-event', compact(
            'organization', 'event', 'totalRegistered', 'confirmed',
            'checkedIn', 'noShows', 'cancelled', 'attendanceRate',
            'noShowRate', 'byCompany', 'byTicketTier', 'revenue',
            'sessionBreakdown'
        ));
    }

    public function export(Request $request, Organization $organization, Event $event)
    {
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'attendances');

        if ($type === 'attendances') {
            return $this->exportAttendances($event, $format);
        }

        if ($type === 'checkins') {
            return $this->exportCheckIns($event, $format);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function exportAttendances(Event $event, string $format)
    {
        $attendances = $event->attendances()->with('eventTicket')->get();

        if ($format === 'csv') {
            $filename = "event-{$event->slug}-attendances.csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($attendances) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Ticket Number', 'First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Job Title', 'Ticket Tier', 'Status', 'Checked In', 'Registration Date']);

                foreach ($attendances as $a) {
                    fputcsv($file, [
                        $a->ticket_number,
                        $a->first_name,
                        $a->last_name,
                        $a->email,
                        $a->phone,
                        $a->company,
                        $a->job_title,
                        $a->eventTicket?->tier_name,
                        $a->registration_status,
                        $a->total_check_ins > 0 ? 'Yes' : 'No',
                        $a->registration_date->format('Y-m-d H:i'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'Unsupported format.');
    }

    private function exportCheckIns(Event $event, string $format)
    {
        $checkIns = CheckIn::whereHas('attendance', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->with(['attendance', 'eventSchedule', 'staff'])
            ->get();

        if ($format === 'csv') {
            $filename = "event-{$event->slug}-checkins.csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($checkIns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Attendance Name', 'Email', 'Session', 'Type', 'Method', 'Staff', 'Time']);

                foreach ($checkIns as $ci) {
                    fputcsv($file, [
                        $ci->attendance->full_name,
                        $ci->attendance->email,
                        $ci->eventSchedule?->name,
                        $ci->check_in_type,
                        $ci->check_in_method,
                        $ci->staff?->name,
                        $ci->created_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'Unsupported format.');
    }
}
