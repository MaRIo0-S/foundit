<?php

namespace App\Http\Middleware;

use App\Models\Claim;
use App\Services\NotificationInboxService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->only('id', 'name', 'email', 'created_at'),
                'role' => $request->user()?->role,
                'is_admin' => $request->user()?->isAdmin() ?? false,
            ],
            'adminNav' => fn () => $request->user()?->isAdmin()
                ? ['pendingClaims' => Claim::where('status', 'pending')->count()]
                : null,
            'notifications' => fn () => $request->user()
                ? app(NotificationInboxService::class)->inboxFor($request->user())->values()
                : [],
            'unreadNotifications' => fn () => $request->user()
                ? app(NotificationInboxService::class)->unreadCount($request->user())
                : 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
