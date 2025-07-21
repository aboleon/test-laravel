<?php

namespace Database\Seeders\Program;

use App\Models\EventManager\Program\EventProgramInterventionOrator;
use Illuminate\Database\Seeder;

class EventContactInterventionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventProgramInterventionOrator::updateOrCreate(["id" => 1], [
            'events_contacts_id' => 11,
            'event_program_intervention_id' => 1,
        ]);
        EventProgramInterventionOrator::updateOrCreate(["id" => 2], [
            'events_contacts_id' => 11,
            'event_program_intervention_id' => 2,
        ]);
        EventProgramInterventionOrator::updateOrCreate(["id" => 3], [
            'events_contacts_id' => 11,
            'event_program_intervention_id' => 3,
        ]);
        EventProgramInterventionOrator::updateOrCreate(["id" => 4], [
            'events_contacts_id' => 11,
            'event_program_intervention_id' => 4,
        ]);
        EventProgramInterventionOrator::updateOrCreate(["id" => 5], [
            'events_contacts_id' => 22,
            'event_program_intervention_id' => 5,
        ]);
        EventProgramInterventionOrator::updateOrCreate(["id" => 6], [
            'events_contacts_id' => 22,
            'event_program_intervention_id' => 6,
        ]);
    }
}
