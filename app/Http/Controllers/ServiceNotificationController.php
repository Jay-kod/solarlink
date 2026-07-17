<?php

namespace App\Http\Controllers;

use App\Models\ServiceNotification;

class ServiceNotificationController extends Controller
{
    public function markRead(ServiceNotification $notification)
    {
        $user = auth()->user();
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return redirect()->back();
    }
}
