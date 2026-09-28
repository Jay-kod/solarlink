<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'solar_appliance_id',
        'technician_profile_id',
        'fault_type',
        'issue_type',
        'priority',
        'severity',
        'description',
        'location',
        'location_address',
        'latitude',
        'longitude',
        'status',
        'payment_status',
        'cost',
        'estimated_cost',
        'scheduled_date',
        'scheduled_time',
        'technician_notes',
        'customer_notes',
        'started_at',
        'completed_at',
        'resolved_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'cost' => 'float',
        'estimated_cost' => 'float',
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appliance()
    {
        return $this->belongsTo(SolarAppliance::class, 'solar_appliance_id');
    }

    public function technicianProfile()
    {
        return $this->belongsTo(TechnicianProfile::class);
    }

    public function photos()
    {
        return $this->hasMany(MaintenanceRequestPhoto::class);
    }

    public function scopeVisibleToUser($query, User $user)
    {
        return $query->where('user_id', $user->id)
            ->orWhereHas('technicianProfile', fn ($q) => $q->where('user_id', $user->id));
    }

    public function transitionTo(string $newStatus): void
    {
        $allowed = [
            'reported' => ['pending', 'cancelled'],
            'pending' => ['assigned', 'cancelled'],
            'assigned' => ['accepted', 'rejected', 'scheduled', 'cancelled'],
            'accepted' => ['scheduled', 'in_progress', 'rejected', 'cancelled'],
            'scheduled' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $current = $this->status;
        if ($newStatus === $current) {
            return;
        }

        $valid = $allowed[$current] ?? [];
        if (!in_array($newStatus, $valid, true) && !empty($valid)) {
            throw new \InvalidArgumentException("Invalid status transition from {$current} to {$newStatus}.");
        }

        $this->status = $newStatus;
        if ($newStatus === 'completed') {
            $this->completed_at = now();
        }
        if ($newStatus === 'in_progress' && empty($this->started_at)) {
            $this->started_at = now();
        }
        if ($newStatus === 'cancelled' && empty($this->completed_at)) {
            $this->completed_at = null;
        }
        $this->save();
    }
}
