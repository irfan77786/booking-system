<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'booking_id' ,'booker_id', 'vehicle_id', 'pickup_location', 'dropoff_location',
        'pickup_date', 'pickup_time', 'return_date', 'return_time', 'total_price', 'payment_status','return_service_id','note', 'round_trip'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function booker() {
        return $this->belongsTo(Booker::class);
    }

    public function passengers() {
        return $this->hasMany(Passenger::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

public function returnService()
{
    return $this->belongsTo(ReturnService::class);
}

public function breakdown()
{
    return $this->hasOne(BookingBreakdown::class);
}

    public function setPickupTimeAttribute($value)
    {
        $this->attributes['pickup_time'] = $this->normalizeTime($value);
    }

    public function setReturnTimeAttribute($value)
    {
        $this->attributes['return_time'] = $this->normalizeTime($value);
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
