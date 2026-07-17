<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Routes users back to the role-specific login portal they were trying to access.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        $path = $request->path();
        if (str_starts_with($path, 'technician')) return route('technician.login');
        if (str_starts_with($path, 'vendor'))     return route('vendor.login');
        if (str_starts_with($path, 'admin'))      return route('admin.login');
        return route('user.login');
    }
}
