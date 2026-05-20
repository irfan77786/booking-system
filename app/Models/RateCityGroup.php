<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateCityGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cities',
    ];

    protected $casts = [
        'cities' => 'array',
    ];

    public function vehicleRates()
    {
        return $this->hasMany(RateCityGroupVehicleRate::class, 'group_id');
    }

    /**
     * Match pickup text to the newest group that contains a city name, then the vehicle row for that group.
     * Same matching order as {@see \App\Http\Controllers\BookingController::calculateDistanceBasedPrice()}.
     */
    public static function vehicleRateForPickup(Vehicle $vehicle, ?string $pickupLocation): ?RateCityGroupVehicleRate
    {
        if ($pickupLocation === null || trim($pickupLocation) === '') {
            return null;
        }

        $groups = self::orderBy('id', 'desc')->get();

        foreach ($groups as $group) {
            $citiesInGroup = is_array($group->cities) ? $group->cities : json_decode($group->cities, true);

            foreach (($citiesInGroup ?? []) as $cityData) {
                $cityName = trim((string) ($cityData['name'] ?? ''));

                if ($cityName !== '' && stripos($pickupLocation, $cityName) !== false) {
                    $groupVehicleRate = RateCityGroupVehicleRate::query()
                        ->where('group_id', $group->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->first();

                    if ($groupVehicleRate) {
                        return $groupVehicleRate;
                    }
                }
            }
        }

        return null;
    }
}

