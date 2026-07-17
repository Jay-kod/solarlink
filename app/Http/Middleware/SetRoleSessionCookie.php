<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetRoleSessionCookie
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $segment = $request->segment(1) ?: $request->input('role');

        if (in_array($segment, ['user', 'technician', 'vendor', 'admin', 'preview'], true)) {
            config(['session.cookie' => 'solarlink_' . Str::slug($segment, '_') . '_session']);
        }

        return $next($request);
    }
}