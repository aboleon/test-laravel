<?php

namespace Database\Seeders\Place;

use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\Place;
use App\Models\PlaceAddress;
use Illuminate\Database\Seeder;

class PlaceAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 10; $i <= 14; $i++) {
            PlaceAddress::updateOrCreate(['id' => $i], ['place_id' => $i, "country_code" => 'fr', 'postal_code' => 75000, 'locality' => 'Paris', 'text_address' => '1 rue de la Paix']);
        }
    }
}
