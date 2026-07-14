<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'original_price',
        'rating',
        'reviews_count',
        'image',
        'description',
        'specs',
        'stock',
        'features',
        'is_featured',
    ];

    protected $casts = [
        'specs' => 'json',
        'features' => 'json',
        'is_featured' => 'boolean',
        'price' => 'float',
        'original_price' => 'float',
        'rating' => 'float',
    ];
}
