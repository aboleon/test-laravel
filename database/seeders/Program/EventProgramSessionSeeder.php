<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramSession;
use Illuminate\Database\Seeder;

class EventProgramSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the event_program_sessions table
        EventProgramSession::updateOrCreate(['id' => 1], ['event_program_day_room_id' => 31, 'session_type_id' => 62, 'position' => 1, 'name' => 'cardio']);
        EventProgramSession::updateOrCreate(['id' => 2], ['event_program_day_room_id' => 31, 'session_type_id' => 62, 'position' => 2, 'name' => 'neurologie']);
        EventProgramSession::updateOrCreate(['id' => 3], ['event_program_day_room_id' => 32, 'session_type_id' => 62, 'position' => 1, 'name' => 'cardio']);
        EventProgramSession::updateOrCreate(['id' => 4], ['event_program_day_room_id' => 31, 'session_type_id' => 62, 'position' => 3, 'name' => 'syndrome']);
    }
}
