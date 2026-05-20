<?php 
// app/Models/ReturnService.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ReturnService extends Model
{
    use HasFactory;
    protected $table= 'return_service';
    protected $fillable = [
        'vehicle_id',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function setPickupTimeAttribute($value)
    {
        $this->attributes['pickup_time'] = $this->normalizeTime($value);
    }

    protected function normalizeTime($value)
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
