<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ($request->is('preview/*')) {
            return [
                ...parent::share($request),
                'auth' => [
                    'user' => null,
                ],
                'cartCount' => 0,
                'wishlistCount' => 0,
                'unreadNotifications' => 0,
                'notifications' => [],
            ];
        }

        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'cartCount' => fn () => $user ? (int) $user->cartItems()->sum('quantity') : 0,
            'wishlistCount' => fn () => $user ? (int) $user->wishlistItems()->count() : 0,
            'unreadNotifications' => fn () => $user
                ? (int) $user->serviceNotifications()->whereNull('read_at')->count()
                : 0,
            'notifications' => fn () => $user
                ? $user->serviceNotifications()
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'title' => $notification->title,
                            'description' => $notification->body,
                            'timestamp' => $notification->created_at?->diffForHumans() ?? '',
                            'type' => $notification->type,
                            'read' => $notification->read_at !== null,
                            'link' => $notification->link,
                        ];
                    })
                    ->values()
                    ->toArray()
                : [],
        ];
    }
}
