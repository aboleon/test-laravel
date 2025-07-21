<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramIntervention;
use Illuminate\Database\Seeder;

class TestEventProgramInterventionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the event_program_interventions table
        EventProgramIntervention::updateOrCreate(['id' => 1], ['event_program_session_id' => 1, 'position' => 1, 'place_room_id' => 1, 'name' => 'cardiovascularité', 'duration' => 120]);
        EventProgramIntervention::updateOrCreate(['id' => 2], ['event_program_session_id' => 1, 'position' => 2, 'place_room_id' => 2, 'name' => 'micro-cardio', 'duration' => 65]);
        EventProgramIntervention::updateOrCreate(['id' => 3], ['event_program_session_id' => 2, 'position' => 1, 'place_room_id' => 1, 'name' => 'neurologie de demain', 'duration' => 60]);
        EventProgramIntervention::updateOrCreate(['id' => 4], ['event_program_session_id' => 3, 'position' => 1, 'place_room_id' => 1, 'name' => 'cardiovascularité', 'duration' => 60]);
        EventProgramIntervention::updateOrCreate(['id' => 5], ['event_program_session_id' => 3, 'position' => 2, 'place_room_id' => 1, 'name' => 'cardio stops', 'duration' => 60]);
        EventProgramIntervention::updateOrCreate(['id' => 6], ['event_program_session_id' => 4, 'position' => 1, 'place_room_id' => 1, 'name' => 'répercussions syndrome', 'duration' => 60]);
    }
}
