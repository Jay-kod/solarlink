<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): Response
    {
        // Only seed demo data in local dev when the database is empty
        try {
            if (app()->environment('local') && \App\Models\User::count() === 0) {
                $this->ensureDemoUsersExist();
            }
        } catch (\Exception $e) {
            // Database is likely offline or unconfigured. Ignore so the UI can still render.
        }

        $role = 'customer';
        $path = $request->path();
        if (str_contains($path, 'vendor')) {
            $role = 'vendor';
        } elseif (str_contains($path, 'technician')) {
            $role = 'technician';
        } elseif (str_contains($path, 'admin')) {
            $role = 'admin';
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'role' => $role,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Only seed demo data in local dev when the database is empty
        try {
            if (app()->environment('local') && \App\Models\User::count() === 0) {
                $this->ensureDemoUsersExist();
            }
        } catch (\Exception $e) {
            // Ignore DB offline exception
        }

        $request->authenticate();

        // SECURITY: Verify the authenticated user's role matches the portal they logged in from.
        $user = Auth::user();
        $submittedRole = $request->input('role');

        if ($submittedRole && $user->role !== $submittedRole) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'These credentials do not match a ' . $submittedRole . ' account.',
            ]);
        }

        $request->session()->regenerate();

        $redirectUrl = $user->role === 'customer' ? '/user' : '/' . $user->role;
        return redirect()->intended($redirectUrl);
    }

    /**
     * Ensure default demo users and chat channels exist in the database on-the-fly.
     */
    private function ensureDemoUsersExist(): void
    {
        // 1. Customer Clara
        \App\Models\User::firstOrCreate(
            ['email' => 'customer@solarlink.io'],
            [
                'name' => 'Clara Oswald',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // 2. Technician Marcus
        $techMarcus = \App\Models\User::firstOrCreate(
            ['email' => 'technician@solarlink.io'],
            [
                'name' => 'Marcus Vance',
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]
        );
        if (!$techMarcus->technicianProfile()->exists()) {
            $techMarcus->technicianProfile()->create([
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'NABCEP PV Installation Professional',
                'rating' => 4.90,
                'review_count' => 142,
                'distance' => '1.2 miles',
                'skills' => ['Inverter Repair', 'Battery Storage Setup', 'Panel Cleaning'],
                'hourly_rate' => 85.00,
                'status' => 'online',
                'experience' => '6 years exp',
                'lat' => 37.7749000,
                'lng' => -122.4194000,
                'eta' => '12 mins'
            ]);
        }

        // 3. Technician Elena
        $techElena = \App\Models\User::firstOrCreate(
            ['email' => 'elena@solarlink.io'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]
        );
        if (!$techElena->technicianProfile()->exists()) {
            $techElena->technicianProfile()->create([
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'Certified Solar Installer',
                'rating' => 4.80,
                'review_count' => 96,
                'distance' => '2.5 miles',
                'skills' => ['Solar Panel Installation', 'Electrical Wiring', 'System Diagnostics'],
                'hourly_rate' => 95.00,
                'status' => 'online',
                'experience' => '4 years exp',
                'lat' => 37.7833000,
                'lng' => -122.4167000,
                'eta' => '18 mins'
            ]);
        }

        // 4. Vendor Sarah
        $vendor = \App\Models\User::firstOrCreate(
            ['email' => 'vendor@solarlink.io'],
            [
                'name' => 'Sarah Smith',
                'password' => Hash::make('password'),
                'role' => 'vendor',
            ]
        );
        if (!$vendor->vendorProfile()->exists()) {
            $vendor->vendorProfile()->create([
                'store_name' => 'EcoGrid Wholesale Direct',
                'company_address' => 'Suite 42, Port Industrial, Oakland, CA',
                'vat_number' => 'US-VAT-90249219',
            ]);
        }

        // 5. Admin
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@solarlink.io'],
            [
                'name' => 'Admin Controller',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 6. Conversations & Messages Seeding
        if (\App\Models\Conversation::count() === 0) {
            $seeder = new \Database\Seeders\ChatSeeder();
            $seeder->run();
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = Auth::user() ? Auth::user()->role : 'customer';

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect based on the user's role (customer -> user/login, others -> role/login)
        $loginPath = $role === 'customer' ? 'user/login' : $role . '/login';
        return redirect($loginPath);
    }
}
