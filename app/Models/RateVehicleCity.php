<?php

    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class RateVehicleCity extends Model
    {
        protected $table = 'rate_vehicle_cities';

        protected $fillable = [
        'vehicle_id',
        'city_place_id',
        'city_name',
        'distance_rates',
        'waiting_time_rates',
        'gratuity',
        'base_rate',
    ];

    protected $casts = [
        'distance_rates'      => 'array',
        'waiting_time_rates'  => 'array',
        'gratuity'            => 'decimal:2',
        'base_rate'           => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
