<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\City;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing cities first
        City::truncate();

        $provincesResponse = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');

        if ($provincesResponse->successful()) {
            $provinces = $provincesResponse->json();

            foreach ($provinces as $provinceData) {
                $regenciesResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceData['id']}.json");

                if ($regenciesResponse->successful()) {
                    $regencies = $regenciesResponse->json();
                    foreach ($regencies as $regencyData) {
                        City::create([
                            'name' => $regencyData['name'],
                        ]);
                    }
                }
            }
        }
    }
}
