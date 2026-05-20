<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id', 'first_name', 'last_name', 'email',
        'phone_number', 'is_booking_for_others','booker_first_name','booker_last_name','booker_email','booker_number'
    ];

    public function booking() {
        return $this->belongsTo(Booking::class);
    }
    public function booker()
    {
        return $this->belongsTo(Booker::class);
    }
    public function flightDetails()
    {
        return $this->hasMany(FlightDetail::class, 'passenger_id');
    }
}
