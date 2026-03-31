<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Cache key prefix for unread notification counts.
     */
    private const CACHE_PREFIX = 'notifications:unread_count:';

    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Get the unread notification count for a user.
     * Uses cache to avoid hitting the database on every request.
     */
    public function getUnreadCount(string|int $userId): int
    {
        return Cache::remember(
            self::CACHE_PREFIX . $userId,
            self::CACHE_TTL,
            fn () => StoreNotification::where('user_id', $userId)
                ->where('is_read', false)
                ->count()
        );
    }

    /**
     * Invalidate the cached unread count for a user.
     */
    public function invalidateCache(string|int $userId): void
    {
        Cache::forget(self::CACHE_PREFIX . $userId);
    }

    /**
     * Increment the cached unread count (when a new notification is created).
     */
    public function incrementUnreadCount(string|int $userId): int
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        if (Cache::has($cacheKey)) {
            $newCount = Cache::increment($cacheKey);
            return $newCount;
        }

        // Cache doesn't exist, fetch fresh count
        return $this->getUnreadCount($userId);
    }

    /**
     * Decrement the cached unread count (when a notification is marked as read).
     */
    public function decrementUnreadCount(string|int $userId, int $amount = 1): int
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        if (Cache::has($cacheKey)) {
            $newCount = Cache::decrement($cacheKey, $amount);
            // Ensure count doesn't go below 0
            if ($newCount < 0) {
                Cache::put($cacheKey, 0, self::CACHE_TTL);
                return 0;
            }
            return $newCount;
        }

        // Cache doesn't exist, fetch fresh count
        return $this->getUnreadCount($userId);
    }

    /**
     * Create a notification and broadcast it to the user.
     */
    public function create(array $data): StoreNotification
    {
        $notification = StoreNotification::create(array_merge($data, [
            'created_at' => now(),
        ]));

        // Update cached count and broadcast
        $unreadCount = $this->incrementUnreadCount($notification->user_id);
        
        // Broadcast the notification event
        event(new NotificationCreated(
            $notification->user_id,
            $unreadCount,
            $notification
        ));

        return $notification;
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(StoreNotification $notification): void
    {
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
            $this->decrementUnreadCount($notification->user_id);
            
            // Broadcast updated count
            event(new NotificationCreated(
                $notification->user_id,
                $this->getUnreadCount($notification->user_id)
            ));
        }
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(string|int $userId): int
    {
        $count = StoreNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($count > 0) {
            // Reset cache to 0
            Cache::put(self::CACHE_PREFIX . $userId, 0, self::CACHE_TTL);
            
            // Broadcast updated count
            event(new NotificationCreated($userId, 0));
        }

        return $count;
    }

    /**
     * Create notifications for multiple users.
     */
    public function createForUsers(array $userIds, array $baseData): void
    {
        foreach ($userIds as $userId) {
            $this->create(array_merge($baseData, ['user_id' => $userId]));
        }
    }
}
