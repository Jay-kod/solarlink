<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'desc',
        'features',
        'cta',
        'href',
        'popular',
    ];

    protected $casts = [
        'features' => 'json',
        'popular' => 'boolean',
        'price' => 'float',
    ];
}
