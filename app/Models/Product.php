<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'vendor_id',
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
        'is_verified',
        'approval_status',
    ];

    protected $casts = [
        'specs' => 'json',
        'features' => 'json',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'price' => 'float',
        'original_price' => 'float',
        'rating' => 'float',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
