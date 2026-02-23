<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBroadcastJob;
use App\Models\Broadcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BroadcastController extends Controller
{
    /**
     * GET /admin/broadcasts — list all broadcasts.
     */
    public function index(): View
    {
        $broadcasts = Broadcast::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    /**
     * GET /admin/broadcasts/create — show create form.
     */
    public function create(): View
    {
        return view('admin.broadcasts.create');
    }

    /**
     * POST /admin/broadcasts — store new broadcast.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string',
            'sms_message'  => 'nullable|string|max:160',
            'type'         => 'required|in:offer,event,announcement',
            'target'       => 'required|in:all,Silver,Gold,Platinum,walkin,guests',
            'channels'     => 'required|in:email,sms,both',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $broadcast = Broadcast::create([
            ...$data,
            'status'     => $data['scheduled_at'] ? 'scheduled' : 'draft',
            'created_by' => auth()->id(),
        ]);

        if ($request->input('action') === 'send') {
            $this->dispatchBroadcast($broadcast);
            return redirect()
                ->route('admin.broadcasts.index')
                ->with('success', 'Broadcast is being sent.');
        }

        return redirect()
            ->route('admin.broadcasts.index')
            ->with('success', 'Broadcast saved as draft.');
    }

    /**
     * POST /admin/broadcasts/{broadcast}/send — manually send a draft/scheduled broadcast.
     */
    public function send(Broadcast $broadcast): RedirectResponse
    {
        abort_if($broadcast->status === 'sent', 422, 'Already sent.');

        $this->dispatchBroadcast($broadcast);

        return redirect()
            ->route('admin.broadcasts.index')
            ->with('success', "Broadcast '{$broadcast->title}' queued for sending.");
    }

    /**
     * Dispatch the broadcast job.
     */
    private function dispatchBroadcast(Broadcast $broadcast): void
    {
        $broadcast->update(['status' => 'sending']);
        SendBroadcastJob::dispatch($broadcast)->onQueue('broadcasts');
    }
}
