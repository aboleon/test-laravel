<?php

namespace Database\Seeders\ParticipationType;


use App\Models\ParticipationType;
use Illuminate\Database\Seeder;

class ParticipationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParticipationType::updateOrCreate(["id" => 2], [
            'group' => "orator",
            'name' => "Comité scientifique",
        ]);
        ParticipationType::updateOrCreate(["id" => 3], [
            'group' => "orator",
            'name' => "Fondateurs",
        ]);
        ParticipationType::updateOrCreate(["id" => 4], [
            'group' => "congress",
            'name' => "Participant",
        ]);
        ParticipationType::updateOrCreate(["id" => 5], [
            'group' => "congress",
            'name' => "Eposter presenter",
        ]);
        ParticipationType::updateOrCreate(["id" => 6], [
            'group' => "industry",
            'name' => "Industriel",
        ]);
        ParticipationType::updateOrCreate(["id" => 7], [
            'group' => "industry",
            'name' => "Industriel non sponsor",
        ]);
    }
}
