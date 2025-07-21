<?php

namespace Database\Seeders\Place;

use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\Place;
use App\Models\PlaceRoom;
use Illuminate\Database\Seeder;

class PlaceRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        PlaceRoom::updateOrCreate(['id' => 10], ['place_id' => 10, 'name' => 'Salon Étoile', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 11], ['place_id' => 10, 'name' => 'Chambre Lumière', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 12], ['place_id' => 10, 'name' => 'Espace Violette', 'level' => '1']);

        PlaceRoom::updateOrCreate(['id' => 13], ['place_id' => 11, 'name' => 'Salon Harmonie', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 14], ['place_id' => 11, 'name' => 'Chambre Echo', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 15], ['place_id' => 11, 'name' => 'Espace Azur', 'level' => '1']);

        PlaceRoom::updateOrCreate(['id' => 16], ['place_id' => 12, 'name' => 'Salon Horizon', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 17], ['place_id' => 12, 'name' => 'Chambre Céleste', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 18], ['place_id' => 12, 'name' => 'Espace Aurore', 'level' => '1']);

        PlaceRoom::updateOrCreate(['id' => 19], ['place_id' => 13, 'name' => 'Salon Jardiné', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 20], ['place_id' => 13, 'name' => 'Chambre Rosée', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 21], ['place_id' => 13, 'name' => 'Espace Sylvan', 'level' => '1']);

        PlaceRoom::updateOrCreate(['id' => 22], ['place_id' => 14, 'name' => 'Salon Cosmos', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 23], ['place_id' => 14, 'name' => 'Chambre Galaxie', 'level' => '1']);
        PlaceRoom::updateOrCreate(['id' => 24], ['place_id' => 14, 'name' => 'Espace Lune', 'level' => '1']);

    }
}
