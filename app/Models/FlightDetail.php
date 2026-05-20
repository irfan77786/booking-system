<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightDetail extends Model
{
    use HasFactory;

    protected $table = 'flight_details';

    protected $fillable = [
        'passenger_id',
        'pickup_flight_details',
        'flight_number',
        'meet_option',
        'no_flight_info',
      
        'inside_pickup_fee',
    ];

    protected $casts = [
        'no_flight_info' => 'boolean',
        
        'inside_pickup_fee' => 'decimal:2',
    ];

    /**
     * Relationship: FlightDetail belongs to a Passenger.
     */
    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id');
    }
}
