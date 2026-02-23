<?php

namespace App\Http\Controllers;

use App\Models\StoreNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * GET /notifications — list all notifications for current user.
     */
    public function index(): View
    {
        $notifications = StoreNotification::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(30);

        // Mark all as read
        StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * GET /notifications/unread-count — called by navbar JS polling.
     */
    public function unreadCount(): JsonResponse
    {
        $count = StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * POST /notifications/{notification}/read — mark single notification as read and redirect.
     */
    public function markRead(StoreNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->update(['is_read' => true]);

        return redirect($notification->action_url ?? route('dashboard'));
    }
}
