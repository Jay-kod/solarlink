<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'maintenance_request_id',
        'title',
        'description',
        'status',
        'selected_quote_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function items()
    {
        return $this->hasMany(ProcurementRequestItem::class);
    }

    public function quotes()
    {
        return $this->hasMany(ProcurementQuote::class);
    }

    public function selectedQuote()
    {
        return $this->belongsTo(ProcurementQuote::class, 'selected_quote_id');
    }
}
