<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateVehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'distance_rates',
        'waiting_time_rates',
        'gratuity',
        'base_rate',
    ];

    protected $casts = [
        'distance_rates' => 'array',
        'waiting_time_rates' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function breakDown()
    {
        return $this->hasMany(BreakDown::class);
    }
}
