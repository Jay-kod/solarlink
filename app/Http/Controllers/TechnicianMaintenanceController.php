<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\ServiceNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechnicianMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = $user?->technicianProfile;

        $requests = MaintenanceRequest::with(['user', 'appliance', 'technicianProfile.user'])
            ->when($profile, function ($query) use ($profile) {
                $query->where('technician_profile_id', $profile->id)
                    ->orWhere(function ($inner) {
                        $inner->where('status', 'pending')->whereNull('technician_profile_id');
                    });
            }, function ($query) {
                $query->where('id', 0);
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'customer' => $item->user?->name,
                    'faultType' => $item->fault_type ?? $item->issue_type ?? 'other',
                    'priority' => $item->priority ?? $item->severity ?? 'medium',
                    'status' => $item->status,
                    'paymentStatus' => $item->payment_status,
                    'location' => $item->location ?? $item->location_address,
                    'appliance' => $item->appliance?->name,
                    'cost' => (float) ($item->cost ?? $item->estimated_cost ?? 0),
                    'scheduledDate' => $item->scheduled_date ? $item->scheduled_date->format('Y-m-d') : null,
                    'createdAt' => $item->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Technician/Jobs', [
            'requests' => $requests,
        ]);
    }

    public function show(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeTechnician($request, $maintenanceRequest);

        return Inertia::render('Technician/Jobs', [
            'request' => $maintenanceRequest,
            'requests' => [$maintenanceRequest],
        ]);
    }

    public function accept(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $user = $request->user();
        $profile = $user?->technicianProfile;

        abort_unless($profile && $maintenanceRequest->technician_profile_id === $profile->id, 403);
        abort_if($maintenanceRequest->status === 'rejected', 422, 'This request was rejected and cannot be accepted.');

        $maintenanceRequest->update([
            'status' => 'accepted',
            'customer_notes' => $maintenanceRequest->customer_notes,
        ]);
        $this->notifyUser($maintenanceRequest, 'Technician accepted', 'Technician accepted maintenance request #' . $maintenanceRequest->id . '.');

        return redirect()->back()->with('success', 'Maintenance request accepted.');
    }

    public function reject(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $user = $request->user();
        $profile = $user?->technicianProfile;

        abort_unless($profile && $maintenanceRequest->technician_profile_id === $profile->id, 403);

        $maintenanceRequest->update([
            'status' => 'rejected',
            'technician_notes' => 'Rejected by technician',
        ]);
        $this->notifyUser($maintenanceRequest, 'Technician rejected', 'Technician rejected maintenance request #' . $maintenanceRequest->id . '.');

        return redirect()->back()->with('success', 'Maintenance request rejected.');
    }

    public function start(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeTechnician($request, $maintenanceRequest);

        $maintenanceRequest->update([
            'status' => 'in_progress',
            'started_at' => $maintenanceRequest->started_at ?? now(),
        ]);
        $this->notifyUser($maintenanceRequest, 'Maintenance started', 'Maintenance work has started for request #' . $maintenanceRequest->id . '.');

        return redirect()->back()->with('success', 'Maintenance started.');
    }

    public function complete(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorizeTechnician($request, $maintenanceRequest);

        $maintenanceRequest->update([
            'status' => 'completed',
            'completed_at' => $maintenanceRequest->completed_at ?? now(),
            'cost' => $maintenanceRequest->cost ?? $maintenanceRequest->estimated_cost ?? 0,
        ]);
        $this->notifyUser($maintenanceRequest, 'Maintenance completed', 'Maintenance request #' . $maintenanceRequest->id . ' has been completed.');

        return redirect()->back()->with('success', 'Maintenance completed.');
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:assigned,accepted,scheduled,in_progress,completed'],
        ]);

        $this->authorizeTechnician($request, $maintenanceRequest);

        $maintenanceRequest->update([
            'status' => $validated['status'],
            'started_at' => $validated['status'] === 'in_progress' && empty($maintenanceRequest->started_at) ? now() : $maintenanceRequest->started_at,
            'completed_at' => $validated['status'] === 'completed' ? ($maintenanceRequest->completed_at ?? now()) : $maintenanceRequest->completed_at,
        ]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    protected function authorizeTechnician(Request $request, MaintenanceRequest $maintenanceRequest): void
    {
        $user = $request->user();
        $profile = $user?->technicianProfile;

        abort_unless($profile && $maintenanceRequest->technician_profile_id === $profile->id, 403);
    }

    protected function notifyUser(MaintenanceRequest $maintenanceRequest, string $title, string $body): void
    {
        ServiceNotification::create([
            'user_id' => $maintenanceRequest->user_id,
            'title' => $title,
            'body' => $body,
            'type' => 'info',
            'link' => '/user/maintenance/' . $maintenanceRequest->id,
        ]);
    }
}

