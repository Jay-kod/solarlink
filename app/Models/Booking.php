<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technician_profile_id',
        'service_type',
        'date',
        'time',
        'status',
        'payment_status',
        'cost',
        'location',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function technicianProfile()
    {
        return $this->belongsTo(TechnicianProfile::class);
    }
}
