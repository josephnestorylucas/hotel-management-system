<?php

namespace App\Http\Controllers;

use App\Models\StoreNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * GET /notifications — list all notifications for current user.
     */
    public function index(): View
    {
        $notifications = StoreNotification::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(30);

        // Mark all as read using the service (updates cache & broadcasts)
        $this->notificationService->markAllAsRead(auth()->id());

        return view('notifications.index', compact('notifications'));
    }

    /**
     * GET /notifications/unread-count — fallback for clients without WebSocket support.
     * Now uses cached count instead of querying database directly.
     */
    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(auth()->id());

        return response()->json(['count' => $count]);
    }

    /**
     * POST /notifications/{notification}/read — mark single notification as read and redirect.
     */
    public function markRead(StoreNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        
        // Use service to mark as read (updates cache & broadcasts)
        $this->notificationService->markAsRead($notification);

        return redirect($notification->action_url ?? route('dashboard'));
    }
}
