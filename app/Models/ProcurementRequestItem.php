<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'product_id',
        'part_name',
        'quantity',
        'notes',
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
