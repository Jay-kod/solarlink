<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'cert_name',
        'rating',
        'review_count',
        'distance',
        'skills',
        'status',
        'approval_status',
        'verification_status',
        'lat',
        'lng',
        'latitude',
        'longitude',
        'service_radius',
        'eta',
        'experience',
        'hourly_rate',
        'cert_file_path',
    ];

    protected $casts = [
        'skills' => 'json',
        'latitude' => 'float',
        'longitude' => 'float',
        'service_radius' => 'float',
        'rating' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function getLatAttribute(): ?float
    {
        return $this->latitude;
    }

    public function setLatAttribute($value): void
    {
        $this->attributes['latitude'] = $value;
    }

    public function getLngAttribute(): ?float
    {
        return $this->longitude;
    }

    public function setLngAttribute($value): void
    {
        $this->attributes['longitude'] = $value;
    }
}
