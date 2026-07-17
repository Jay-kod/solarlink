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
        'lat',
        'lng',
        'eta',
        'experience',
        'hourly_rate',
        'cert_file_path',
    ];

    protected $casts = [
        'skills' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
