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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
