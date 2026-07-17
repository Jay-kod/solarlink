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
        $profile = $user->technicianProfile;

        $requests = MaintenanceRequest::with(['user', 'appliance'])
            ->where(function ($query) use ($profile) {
                $query->where('status', 'open');
                if ($profile) {
                    $query->orWhere('technician_profile_id', $profile->id);
                }
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'customer' => $item->user->name,
                    'issueType' => $item->issue_type,
                    'severity' => $item->severity,
                    'status' => $item->status,
                    'paymentStatus' => $item->payment_status,
                    'location' => $item->location_address,
                    'appliance' => $item->appliance?->name,
                    'estimatedCost' => (float) $item->estimated_cost,
                    'createdAt' => $item->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Technician/Requests/Index', [
            'requests' => $requests,
        ]);
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:assigned,in_progress,resolved'],
        ]);

        $user = $request->user();
        $profile = $user->technicianProfile;

        if (!$profile) {
            abort(403);
        }

        if ($validated['status'] === 'assigned' && $maintenanceRequest->technician_profile_id && $maintenanceRequest->technician_profile_id !== $profile->id) {
            abort(403);
        }

        $maintenanceRequest->update([
            'technician_profile_id' => $maintenanceRequest->technician_profile_id ?: $profile->id,
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        ServiceNotification::create([
            'user_id' => $maintenanceRequest->user_id,
            'title' => 'Maintenance Status Updated',
            'body' => 'Ticket #' . $maintenanceRequest->id . ' is now ' . str_replace('_', ' ', $validated['status']) . '.',
            'type' => 'info',
            'link' => '/user/maintenance',
        ]);

        return redirect()->back()->with('success', 'Status updated.');
    }
}
