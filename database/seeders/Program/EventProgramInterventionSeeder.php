<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramIntervention;
use Illuminate\Database\Seeder;

class EventProgramInterventionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the event_program_interventions table
        EventProgramIntervention::updateOrCreate(['id' => 1], ['event_program_session_id' => 1, 'position' => 1, 'place_room_id' => 10, 'specificity_id' => 51, 'name' => 'cardiovascularité', 'duration' => 120, "start" => "2023-05-06 09:00:00", "end" => "2023-05-06 11:00:00"]);
        EventProgramIntervention::updateOrCreate(['id' => 2], ['event_program_session_id' => 1, 'position' => 2, 'place_room_id' => 12, 'specificity_id' => 51, 'name' => 'micro-cardio', 'duration' => 65, "preferred_start_time" => "07:40:00", "start" => "2023-05-06 07:40:00", "end" => "2023-05-06 08:45:00"]);
        EventProgramIntervention::updateOrCreate(['id' => 3], ['event_program_session_id' => 2, 'position' => 1, 'place_room_id' => 10, 'specificity_id' => 52, 'name' => 'neurologie de demain', 'duration' => 60, "start" => "2023-05-06 11:00:00", "end" => "2023-05-06 12:00:00"]);
        EventProgramIntervention::updateOrCreate(['id' => 4], ['event_program_session_id' => 3, 'position' => 1, 'place_room_id' => 10, 'specificity_id' => 51, 'name' => 'cardiovascularité', 'duration' => 60, "start" => "2023-05-07 10:00:00", "end" => "2023-05-07 11:00:00"]);
        EventProgramIntervention::updateOrCreate(['id' => 5], ['event_program_session_id' => 3, 'position' => 2, 'place_room_id' => 10, 'specificity_id' => 51, 'name' => 'cardio stops', 'duration' => 60, "start" => "2023-05-07 11:00:00", "end" => "2023-05-07 12:00:00"]);
        EventProgramIntervention::updateOrCreate(['id' => 6], ['event_program_session_id' => 4, 'position' => 1, 'place_room_id' => 10, 'specificity_id' => 51, 'name' => 'répercussions syndrome', 'duration' => 60, "start" => "2023-05-06 12:00:00", "end" => "2023-05-06 13:00:00"]);
    }
}
