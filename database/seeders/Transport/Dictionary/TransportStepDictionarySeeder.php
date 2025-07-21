<?php

namespace Database\Seeders\Transport\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class TransportStepDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 26], ['slug' => 'transport_step', 'name' => "Etape de transport"]);
        DictionnaryEntry::updateOrCreate(['id' => 55], ['dictionnary_id' => 26, 'name' => 'Tout seul - Factures ok']);
        DictionnaryEntry::updateOrCreate(['id' => 56], ['dictionnary_id' => 26, 'name' => 'Master - ok']);

    }
}
