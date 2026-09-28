<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedLocation extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'address',
        'state',
        'local_government',
        'latitude',
        'longitude',
        'is_primary',
        'share_with_users',
        'share_detail',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_primary' => 'boolean',
        'share_with_users' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
