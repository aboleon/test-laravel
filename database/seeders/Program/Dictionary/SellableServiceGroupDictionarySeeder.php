<?php

namespace Database\Seeders\Program\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class SellableServiceGroupDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 9], ['slug' => 'service_family', 'name' => "Familles de prestations"]);
        DictionnaryEntry::updateOrCreate(['id' => 21], ['dictionnary_id' => 9, 'name' => 'Badge']);
        DictionnaryEntry::updateOrCreate(['id' => 22], ['dictionnary_id' => 9, 'name' => 'Ateliers']);
        DictionnaryEntry::updateOrCreate(['id' => 23], ['dictionnary_id' => 9, 'name' => 'Caution']);
        DictionnaryEntry::updateOrCreate(['id' => 24], ['dictionnary_id' => 9, 'name' => 'Dîners']);

    }
}
