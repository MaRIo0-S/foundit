<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationInboxService
{
    public function inboxFor(User $user, int $limit = 20): Collection
    {
        $this->purgeExpiredRead();

        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get([
                'id',
                'type',
                'title',
                'message',
                'is_read',
                'read_at',
                'related_type',
                'related_id',
                'created_at',
            ]);
    }

    public function unreadCount(User $user): int
    {
        $this->purgeExpiredRead();

        return $user->notifications()->where('is_read', false)->count();
    }

    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function purgeExpiredRead(): int
    {
        return once(function () {
            return Notification::query()
                ->where('is_read', true)
                ->whereNotNull('read_at')
                ->where('read_at', '<', now()->subDays(7))
                ->delete();
        });
    }
}
