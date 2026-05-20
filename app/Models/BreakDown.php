<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakDown extends Model
{
    use HasFactory;
    protected $fillable = [
        'rate_vehicle_id',
        'flat_rate',
        'gratuity',
        'state_tax',
        'toll_tax',
        'total_price',
        'stop_cost',
        'state_tax_cost',
        'gratuity_cost',
    ];

    public function rateVehicle()
    {
        return $this->belongsTo(RateVehicle::class);
    }
}
