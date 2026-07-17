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
        'issue_type',
        'severity',
        'description',
        'location_address',
        'latitude',
        'longitude',
        'status',
        'payment_status',
        'estimated_cost',
        'resolved_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'estimated_cost' => 'float',
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
}
