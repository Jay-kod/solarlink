<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\ServiceNotification;
use App\Models\SolarAppliance;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $requests = MaintenanceRequest::with(['appliance', 'technicianProfile.user', 'photos'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'issueType' => $item->issue_type,
                    'severity' => $item->severity,
                    'description' => $item->description,
                    'location' => $item->location_address,
                    'status' => $item->status,
                    'paymentStatus' => $item->payment_status,
                    'estimatedCost' => (float) $item->estimated_cost,
                    'appliance' => $item->appliance ? $item->appliance->name : null,
                    'technician' => $item->technicianProfile?->user?->name,
                    'photos' => $item->photos->map(fn ($photo) => asset('storage/' . $photo->file_path))->values(),
                    'createdAt' => $item->created_at->format('Y-m-d H:i'),
                ];
            });

        $appliances = SolarAppliance::where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $technicians = TechnicianProfile::with('user')
            ->get()
            ->filter(fn ($tech) => $tech->user)
            ->map(fn ($tech) => [
                'id' => $tech->id,
                'name' => $tech->user->name,
                'hourly_rate' => (float) ($tech->hourly_rate ?? 0),
            ])
            ->values();

        $notifications = ServiceNotification::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Customer/Maintenance/Index', [
            'requests' => $requests,
            'appliances' => $appliances,
            'technicians' => $technicians,
            'notifications' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'solar_appliance_id' => ['nullable', 'exists:solar_appliances,id'],
            'technician_profile_id' => ['nullable', 'exists:technician_profiles,id'],
            'issue_type' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'in:low,medium,high,critical'],
            'description' => ['required', 'string'],
            'location_address' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'photos.*' => ['nullable', 'image', 'max:3072'],
        ]);

        $user = $request->user();

        $maintenanceRequest = MaintenanceRequest::create([
            'user_id' => $user->id,
            'solar_appliance_id' => $validated['solar_appliance_id'] ?? null,
            'technician_profile_id' => $validated['technician_profile_id'] ?? null,
            'issue_type' => $validated['issue_type'],
            'severity' => $validated['severity'],
            'description' => $validated['description'],
            'location_address' => $validated['location_address'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => $validated['technician_profile_id'] ? 'assigned' : 'open',
            'payment_status' => 'unpaid',
            'estimated_cost' => (float) ($validated['estimated_cost'] ?? 0),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance_photos', 'public');
                $maintenanceRequest->photos()->create(['file_path' => $path]);
            }
        }

        ServiceNotification::create([
            'user_id' => $user->id,
            'title' => 'Maintenance Ticket Created',
            'body' => 'Ticket #' . $maintenanceRequest->id . ' has been logged and is now being tracked.',
            'type' => 'success',
            'link' => '/user/maintenance',
        ]);

        $technicianUsers = User::where('role', 'technician')->pluck('id');
        foreach ($technicianUsers as $techUserId) {
            ServiceNotification::create([
                'user_id' => $techUserId,
                'title' => 'New Maintenance Ticket',
                'body' => 'A new customer ticket has been created: ' . $validated['issue_type'],
                'type' => 'info',
                'link' => '/technician/requests',
            ]);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request submitted.');
    }

    public function pay(MaintenanceRequest $maintenanceRequest)
    {
        $user = auth()->user();
        if ($maintenanceRequest->user_id !== $user->id) {
            abort(403);
        }

        $maintenanceRequest->update([
            'payment_status' => 'paid',
            'status' => 'paid',
        ]);

        ServiceNotification::create([
            'user_id' => $user->id,
            'title' => 'Maintenance Ticket Paid',
            'body' => 'Payment has been recorded for ticket #' . $maintenanceRequest->id . '.',
            'type' => 'success',
            'link' => '/user/maintenance',
        ]);

        return redirect()->back()->with('success', 'Payment recorded.');
    }
}
