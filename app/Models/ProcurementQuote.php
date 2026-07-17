<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'supplier_id',
        'amount',
        'lead_time_days',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
