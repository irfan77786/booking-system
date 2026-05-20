<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_name',
        'vehicle_code',
        'number_of_passengers',
        'luggage_capacity',
        'active',
        'greeting_fee',
        'description',
        'slug',
    ];

    
    public function rateVehicle()
    {
        return $this->hasMany(RateVehicle::class);
    }

    public function carSeat()
    {
        return $this->hasMany(CarSeat::class);
    }

    // public function getBreakDownAttribute()
    // {
    //     return $this->rateVehicle->breakDown ?? null;
    // }
}
