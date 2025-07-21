<?php

namespace Database\Seeders\Program;

use App\Models\Event;
use App\Models\PlaceRoom;
use Illuminate\Database\Seeder;

class TestProgramDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Seed the events table
        Event::create(['id' => 1]);
        Event::create(['id' => 2]);

        // Seed the place_rooms table
        PlaceRoom::create(['id' => 1, 'name' => 'salle1']);
        PlaceRoom::create(['id' => 2, 'name' => 'salle2']);

        $this->call([
            TestEventProgramDaySeeder::class,
            TestEventProgramSessionSeeder::class,
            TestEventProgramInterventionSeeder::class,
        ]);
    }
}
