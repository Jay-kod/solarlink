<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'company_address',
        'vat_number',
        'approval_status',
        'verification_status',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
