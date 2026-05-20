<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingBreakdown extends Model
{
    use HasFactory;

    protected $table = 'booking_breakdwon'; // still points to the existing table
public $timestamps = false;
    protected $fillable = [
        'booking_id',
        'base_fare',
        'per_km_rate',
        'total_kms',
        'hourly_fare',
        'total_hours',
        'return_base_fare',
        'return_per_km_rate',
        'return_total_kms',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
