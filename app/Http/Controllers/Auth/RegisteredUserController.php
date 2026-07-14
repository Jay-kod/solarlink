<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\TechnicianProfile;
use App\Models\VendorProfile;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $role = 'customer';
        $path = $request->path();
        if (str_contains($path, 'vendor')) {
            $role = 'vendor';
        } elseif (str_contains($path, 'technician')) {
            $role = 'technician';
        }

        return Inertia::render('Auth/Register', [
            'role' => $role,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Basic user validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|in:customer,technician,vendor',
        ];

        // 2. Add role-specific validation rules
        if ($request->role === 'technician') {
            $rules['cert_name'] = 'required|string|max:255';
            $rules['years_experience'] = 'required|string';
            $rules['hourly_rate'] = 'required|numeric|min:0';
            $rules['cert_file'] = 'nullable|file|max:10240'; // max 10MB
        } elseif ($request->role === 'vendor') {
            $rules['store_name'] = 'required|string|max:255';
            $rules['company_address'] = 'required|string|max:255';
            $rules['vat_number'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // 3. Create core user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // 4. Create specific profile
        if ($user->role === 'technician') {
            $certFilePath = null;
            if ($request->hasFile('cert_file')) {
                $certFilePath = $request->file('cert_file')->store('certifications', 'public');
            }

            TechnicianProfile::create([
                'user_id' => $user->id,
                'cert_name' => $request->cert_name,
                'experience' => $request->years_experience,
                'hourly_rate' => $request->hourly_rate,
                'cert_file_path' => $certFilePath,
            ]);
        } elseif ($user->role === 'vendor') {
            VendorProfile::create([
                'user_id' => $user->id,
                'store_name' => $request->store_name,
                'company_address' => $request->company_address,
                'vat_number' => $request->vat_number,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        $redirectUrl = $user->role === 'customer' ? '/user' : '/' . $user->role;
        return redirect($redirectUrl);
    }
}
