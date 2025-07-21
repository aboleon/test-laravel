<?php

namespace Database\Seeders\Program\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class InterventionTypeDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 25], ['slug' => 'program_intervention_types', 'name' => "Type Intervention"]);
        DictionnaryEntry::updateOrCreate(['id' => 51], ['dictionnary_id' => 25, 'name' => 'Intervention simple']);
        DictionnaryEntry::updateOrCreate(['id' => 52], ['dictionnary_id' => 25, 'name' => 'Co-intervenant/Table ronde']);
        DictionnaryEntry::updateOrCreate(['id' => 53], ['dictionnary_id' => 25, 'name' => 'Opposant débat']);

    }
}
