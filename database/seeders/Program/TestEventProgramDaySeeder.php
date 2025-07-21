<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramDayRoom;
use Illuminate\Database\Seeder;

class TestEventProgramDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // event 1
        EventProgramDayRoom::updateOrCreate(['id' => 31], ['event_id' => 1, 'datetime_start' => '2023-05-06 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 32], ['event_id' => 1, 'datetime_start' => '2023-05-07 09:00:00']);

        // event 2
        EventProgramDayRoom::updateOrCreate(['id' => 118], ['event_id' => 2, 'datetime_start' => '2023-04-18 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 119], ['event_id' => 2, 'datetime_start' => '2023-04-19 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 120], ['event_id' => 2, 'datetime_start' => '2023-04-20 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 121], ['event_id' => 2, 'datetime_start' => '2023-04-21 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 122], ['event_id' => 2, 'datetime_start' => '2023-04-22 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 123], ['event_id' => 2, 'datetime_start' => '2023-04-23 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 124], ['event_id' => 2, 'datetime_start' => '2023-04-24 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 125], ['event_id' => 2, 'datetime_start' => '2023-04-25 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 126], ['event_id' => 2, 'datetime_start' => '2023-04-26 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 127], ['event_id' => 2, 'datetime_start' => '2023-04-27 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 128], ['event_id' => 2, 'datetime_start' => '2023-04-28 09:00:00']);
        EventProgramDayRoom::updateOrCreate(['id' => 129], ['event_id' => 2, 'datetime_start' => '2023-04-29 09:00:00']);

    }
}
