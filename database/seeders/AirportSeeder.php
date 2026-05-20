<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Airport::truncate();

        $airports = [
            ['name' => 'American Airlines', 'iata_code' => 'AA'],
            ['name' => 'Delta Air Lines', 'iata_code' => 'DL'],
            ['name' => 'United Airlines', 'iata_code' => 'UA'],
            ['name' => 'Alaska Airlines', 'iata_code' => 'AS'],
            ['name' => 'Hawaiian Airlines', 'iata_code' => 'HA'],
            ['name' => 'JetBlue Airways', 'iata_code' => 'B6'],
            ['name' => 'Southwest Airlines', 'iata_code' => 'WN'],
            ['name' => 'Spirit Airlines', 'iata_code' => 'NK'],
            ['name' => 'Frontier Airlines', 'iata_code' => 'F9'],
            ['name' => 'Allegiant Air', 'iata_code' => 'G4'],
            ['name' => 'Avelo Airlines', 'iata_code' => 'XP'],
            ['name' => 'Breeze Airways', 'iata_code' => 'MX'],
            ['name' => 'Sun Country Airlines', 'iata_code' => 'SY'],
            ['name' => 'SkyWest Airlines', 'iata_code' => 'OO'],
            ['name' => 'Republic Airways', 'iata_code' => 'YX'],
            ['name' => 'Envoy Air', 'iata_code' => 'MQ'],
            ['name' => 'PSA Airlines', 'iata_code' => 'OH'],
            ['name' => 'Endeavor Air', 'iata_code' => '9E'],
            ['name' => 'Mesa Airlines', 'iata_code' => 'YV'],
            ['name' => 'British Airways', 'iata_code' => 'BA'],
            ['name' => 'Virgin Atlantic', 'iata_code' => 'VS'],
            ['name' => 'Norse Atlantic Airways', 'iata_code' => 'N0'],
            ['name' => 'Lufthansa', 'iata_code' => 'LH'],
            ['name' => 'Air France', 'iata_code' => 'AF'],
            ['name' => 'KLM Royal Dutch Airlines', 'iata_code' => 'KL'],
            ['name' => 'Swiss International Air Lines', 'iata_code' => 'LX'],
            ['name' => 'Austrian Airlines', 'iata_code' => 'OS'],
            ['name' => 'Iberia', 'iata_code' => 'IB'],
            ['name' => 'TAP Air Portugal', 'iata_code' => 'TP'],
            ['name' => 'Finnair', 'iata_code' => 'AY'],
            ['name' => 'Scandinavian Airlines', 'iata_code' => 'SK'],
            ['name' => 'ITA Airways', 'iata_code' => 'AZ'],
            ['name' => 'Qatar Airways', 'iata_code' => 'QR'],
            ['name' => 'Turkish Airlines', 'iata_code' => 'TK'],
            ['name' => 'Royal Jordanian', 'iata_code' => 'RJ'],
            ['name' => 'Saudia Airlines', 'iata_code' => 'SV'],
            ['name' => 'Air Canada', 'iata_code' => 'AC'],
            ['name' => 'WestJet', 'iata_code' => 'WS'],
            ['name' => 'Singapore Airlines', 'iata_code' => 'SQ'],
            ['name' => 'Japan Airlines', 'iata_code' => 'JL'],
            ['name' => 'All Nippon Airways', 'iata_code' => 'NH'],
            ['name' => 'Korean Air', 'iata_code' => 'KE'],
            ['name' => 'Cathay Pacific', 'iata_code' => 'CX'],
            ['name' => 'China Airlines', 'iata_code' => 'CI'],
        ];

        foreach ($airports as $airport) {
            \App\Models\Airport::updateOrCreate(
                ['name' => $airport['name']],
                ['iata_code' => $airport['iata_code']]
            );
        }
    }
}
