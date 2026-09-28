<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return $maintenanceRequest->user_id === $user->id || ($maintenanceRequest->technician_profile_id && $maintenanceRequest->technicianProfile && $maintenanceRequest->technicianProfile->user_id === $user->id);
    }

    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($maintenanceRequest->user_id === $user->id) {
            return true;
        }

        return $maintenanceRequest->technicianProfile && $maintenanceRequest->technicianProfile->user_id === $user->id;
    }

    public function cancel(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return $maintenanceRequest->user_id === $user->id || $user->role === 'admin';
    }
}
