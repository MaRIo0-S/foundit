<?php

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(
        User $recipient,
        string $type,
        string $title,
        string $message,
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): Notification {
        return Notification::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'is_read' => false,
        ]);
    }

    public function notifyStaff(string $type, string $title, string $message, ?string $relatedType = null, ?int $relatedId = null): void
    {
        User::query()
            ->where('role', 'admin')
            ->where('is_suspended', false)
            ->each(fn (User $user) => $this->send($user, $type, $title, $message, $relatedType, $relatedId));
    }
}
