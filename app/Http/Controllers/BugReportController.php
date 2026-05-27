<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BugReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'page_url' => 'nullable|string|max:2048',
            'module' => 'required|string|max:100',
            'severity' => 'required|in:low,medium,high,critical',
            'reported_by' => 'nullable|string|max:100',
        ]);

        $report = BugReport::create([
            'title' => $validated['title'],
            'details' => $validated['details'],
            'page_url' => $validated['page_url'] ?? null,
            'module' => $validated['module'],
            'severity' => $validated['severity'],
            'reported_by' => $validated['reported_by'] ?? null,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Bug reported successfully.',
            'id' => $report->id,
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        if (!config('bugs.dashboard_enabled')) {
            abort(404);
        }

        $query = BugReport::query();

        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->string('severity'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->get('export') === 'csv') {
            return $this->exportCsv($query);
        }

        $bugReports = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $stats = [
            'total' => BugReport::count(),
            'open' => BugReport::where('status', 'open')->count(),
            'fixed' => BugReport::where('status', 'fixed')->count(),
        ];

        return view('bugs.index', compact('bugReports', 'stats'));
    }

    public function update(Request $request, BugReport $bugReport)
    {
        if (!config('bugs.dashboard_enabled')) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:open,acknowledged,fixed',
        ]);

        $bugReport->update(['status' => $validated['status']]);

        return back()->with('success', 'Bug status updated.');
    }

    public function destroy(BugReport $bugReport)
    {
        if (!config('bugs.dashboard_enabled')) {
            abort(404);
        }

        $bugReport->delete();

        return back()->with('success', 'Bug report deleted.');
    }

    protected function exportCsv($query)
    {
        $filename = 'bug-reports-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = ['ID', 'Title', 'Module', 'Severity', 'Status', 'Reported By', 'Page URL', 'Created At'];

        return response()->streamDownload(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $query->orderByDesc('created_at')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->title,
                        $row->module,
                        $row->severity,
                        $row->status,
                        $row->reported_by,
                        $row->page_url,
                        $row->created_at,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, $headers);
    }
}
