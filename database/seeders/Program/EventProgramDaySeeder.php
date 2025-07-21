<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramDayRoom;
use Illuminate\Database\Seeder;

class EventProgramDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the event_program_days table
        EventProgramDayRoom::updateOrCreate(['id' => 31], ['event_id' => 1, 'datetime_start' => '2023-05-06 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 32], ['event_id' => 1, 'datetime_start' => '2023-05-07 10:00:00']);

    }
}
