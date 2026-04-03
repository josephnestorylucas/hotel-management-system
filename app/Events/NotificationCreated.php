<?php

namespace App\Events;

use App\Models\StoreNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $userId;
    public int $unreadCount;
    public ?array $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(string $userId, int $unreadCount, ?StoreNotification $notification = null)
    {
        $this->userId = $userId;
        $this->unreadCount = $unreadCount;
        $this->notification = $notification ? [
            'id' => $notification->id,
            'title' => $notification->title,
            'body' => $notification->body,
            'type' => $notification->type,
            'action_url' => $notification->action_url,
            'created_at' => $notification->created_at?->diffForHumans(),
        ] : null;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'unread_count' => $this->unreadCount,
            'notification' => $this->notification,
        ];
    }
}
