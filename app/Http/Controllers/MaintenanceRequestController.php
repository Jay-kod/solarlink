<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\ServiceNotification;
use App\Models\SolarAppliance;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
                    'faultType' => $item->fault_type ?? $item->issue_type ?? 'other',
                    'priority' => $item->priority ?? $item->severity ?? 'medium',
                    'description' => $item->description,
                    'location' => $item->location ?? $item->location_address,
                    'status' => $item->status,
                    'paymentStatus' => $item->payment_status,
                    'cost' => (float) ($item->cost ?? $item->estimated_cost ?? 0),
                    'appliance' => $item->appliance?->name,
                    'technician' => $item->technicianProfile?->user?->name,
                    'photos' => $item->photos->map(fn ($photo) => asset('storage/' . $photo->file_path))->values(),
                    'createdAt' => $item->created_at->format('Y-m-d H:i'),
                ];
            });

        $appliances = SolarAppliance::where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $technicians = TechnicianProfile::with('user')
            ->where('verification_status', 'verified')
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

    public function create(Request $request)
    {
        $user = $request->user();
        $appliances = SolarAppliance::where('user_id', $user->id)->get();

        return Inertia::render('Customer/Maintenance/Create', [
            'appliances' => $appliances,
        ]);
    }

    public function show(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($maintenanceRequest->user_id === $request->user()->id, 403);

        return Inertia::render('Customer/Maintenance/Show', [
            'request' => [
                'id' => $maintenanceRequest->id,
                'faultType' => $maintenanceRequest->fault_type ?? $maintenanceRequest->issue_type ?? 'other',
                'priority' => $maintenanceRequest->priority ?? $maintenanceRequest->severity ?? 'medium',
                'status' => $maintenanceRequest->status,
                'description' => $maintenanceRequest->description,
                'location' => $maintenanceRequest->location ?? $maintenanceRequest->location_address,
                'latitude' => $maintenanceRequest->latitude,
                'longitude' => $maintenanceRequest->longitude,
                'cost' => (float) ($maintenanceRequest->cost ?? $maintenanceRequest->estimated_cost ?? 0),
                'appliance' => $maintenanceRequest->appliance?->name,
                'technician' => $maintenanceRequest->technicianProfile?->user?->name,
                'technicianNotes' => $maintenanceRequest->technician_notes,
                'scheduledDate' => $maintenanceRequest->scheduled_date ? $maintenanceRequest->scheduled_date->format('Y-m-d') : null,
                'scheduledTime' => $maintenanceRequest->scheduled_time,
                'completedAt' => $maintenanceRequest->completed_at ? $maintenanceRequest->completed_at->format('Y-m-d H:i') : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'solar_appliance_id' => ['required', 'exists:solar_appliances,id'],
            'technician_profile_id' => ['nullable', 'exists:technician_profiles,id'],
            'fault_type' => ['required', 'in:inverter,battery,solar_panel,charging,power_output,wiring,electrical,other'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'description' => ['required', 'string', 'min:10'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'photos.*' => ['nullable', 'image', 'max:3072'],
        ]);

        $user = $request->user();
        $appliance = SolarAppliance::find($validated['solar_appliance_id']);

        if (!$appliance || $appliance->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'solar_appliance_id' => ['You can only report faults on your own appliances.'],
            ]);
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'user_id' => $user->id,
            'solar_appliance_id' => $appliance->id,
            'technician_profile_id' => $validated['technician_profile_id'] ?? null,
            'fault_type' => $validated['fault_type'],
            'issue_type' => $validated['fault_type'],
            'priority' => $validated['priority'],
            'severity' => $validated['priority'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'location_address' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => ($validated['technician_profile_id'] ?? null) ? 'assigned' : 'reported',
            'payment_status' => 'unpaid',
            'cost' => (float) ($validated['cost'] ?? 0),
            'estimated_cost' => (float) ($validated['cost'] ?? 0),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance_photos', 'public');
                $maintenanceRequest->photos()->create(['file_path' => $path]);
            }
        }

        ServiceNotification::create([
            'user_id' => $user->id,
            'title' => 'Maintenance request submitted',
            'body' => 'Your maintenance request #' . $maintenanceRequest->id . ' has been received.',
            'type' => 'success',
            'link' => '/user/maintenance/' . $maintenanceRequest->id,
        ]);

        if ($validated['technician_profile_id'] ?? null) {
            $profile = TechnicianProfile::find($validated['technician_profile_id']);
            if ($profile && $profile->user) {
                ServiceNotification::create([
                    'user_id' => $profile->user->id,
                    'title' => 'New maintenance assignment',
                    'body' => 'A new maintenance request has been assigned to you.',
                    'type' => 'info',
                    'link' => '/technician/jobs',
                ]);
            }
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request submitted.');
    }

    public function cancel(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($maintenanceRequest->user_id === $request->user()->id, 403);
        $maintenanceRequest->update(['status' => 'cancelled']);

        ServiceNotification::create([
            'user_id' => $maintenanceRequest->user_id,
            'title' => 'Maintenance cancelled',
            'body' => 'Maintenance request #' . $maintenanceRequest->id . ' was cancelled.',
            'type' => 'warning',
            'link' => '/user/maintenance',
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request cancelled.');
    }

    public function pay(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($maintenanceRequest->user_id === $request->user()->id, 403);

        $maintenanceRequest->update([
            'payment_status' => 'paid',
            'status' => 'pending',
        ]);

        ServiceNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Maintenance payment recorded',
            'body' => 'Payment for request #' . $maintenanceRequest->id . ' has been recorded.',
            'type' => 'success',
            'link' => '/user/maintenance/' . $maintenanceRequest->id,
        ]);

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function assignTechnician(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($request->user()->role === 'admin' || $request->user()->role === 'customer', 403);

        $validated = $request->validate([
            'technician_profile_id' => ['required', 'exists:technician_profiles,id'],
        ]);

        $profile = TechnicianProfile::findOrFail($validated['technician_profile_id']);
        abort_unless($profile->verification_status === 'verified', 422, 'Only verified technicians can be assigned.');

        $maintenanceRequest->update([
            'technician_profile_id' => $profile->id,
            'status' => 'assigned',
        ]);

        ServiceNotification::create([
            'user_id' => $profile->user_id,
            'title' => 'Assigned maintenance request',
            'body' => 'You have been assigned to maintenance request #' . $maintenanceRequest->id . '.',
            'type' => 'info',
            'link' => '/technician/jobs',
        ]);

        return redirect()->back()->with('success', 'Technician assigned.');
    }
}
