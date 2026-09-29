<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateCityGroupVehicleRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
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

    public function group()
    {
        return $this->belongsTo(RateCityGroup::class, 'group_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    

    public static function normalizeDistanceTiers($tiersRaw): array
    {
        if (is_string($tiersRaw)) {
            $decoded = json_decode($tiersRaw, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($tiersRaw) ? $tiersRaw : [];
    }

    

    public static function pointToPointBreakdown(?object $rateModel, Vehicle $vehicle, float $distanceMiles): array
    {
        $base = ($rateModel && isset($rateModel->base_rate))
            ? (float) $rateModel->base_rate
            : (float) $vehicle->base_fare;

        $tiersRaw = ($rateModel && ! empty($rateModel->distance_rates))
            ? $rateModel->distance_rates
            : [];

        $tiers = self::normalizeDistanceTiers($tiersRaw);

        if (empty($tiers)) {
            $fallbackPrice = $base + ((float) $vehicle->per_km_rate * $distanceMiles);

            return [
                'distance_km' => round($distanceMiles, 2),
                'distance_miles' => round($distanceMiles, 2),
                'price' => round($fallbackPrice, 2),
                'baseFare' => $base,
                'hourlyFare' => null,
                'perKmRate' => $vehicle->per_km_rate,
                'hours' => null,
                'type' => 'PointToPoint',
            ];
        }

        $remainingDistance = $distanceMiles;
        $distanceCost = 0.0;

        foreach ($tiers as $tier) {
            if ($remainingDistance <= 0) {
                break;
            }

            $tier = (array) $tier;
            $rate = isset($tier['rate']) ? (float) $tier['rate'] : 0.0;
            $distanceValue = $tier['distance'] ?? null;

            if ($distanceValue === 'remaining') {
                $distanceCost += $remainingDistance * $rate;
                $remainingDistance = 0;
                break;
            }

            $tierDistance = (float) $distanceValue;
            if ($tierDistance <= 0) {
                continue;
            }

            $appliedDistance = min($remainingDistance, $tierDistance);
            $distanceCost += $appliedDistance * $rate;
            $remainingDistance -= $appliedDistance;
        }

        $totalPrice = round($base + $distanceCost, 2);

        return [
            'distance_km' => round($distanceMiles, 2),
            'distance_miles' => round($distanceMiles, 2),
            'price' => $totalPrice,
            'baseFare' => $base,
            'hourlyFare' => null,
            'perKmRate' => null,
            'hours' => null,
            'type' => 'PointToPoint',
        ];
    }

     
    public function breakdownForDistance(Vehicle $vehicle, float $distanceMiles): array
    {
        return self::pointToPointBreakdown($this, $vehicle, $distanceMiles);
    }
}

