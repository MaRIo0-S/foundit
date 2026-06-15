<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\NotificationInboxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationInboxService $inbox) {}

    public function markRead(Request $request): RedirectResponse
    {
        $this->inbox->markAllAsRead($request->user());

        return back();
    }
}
