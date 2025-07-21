<?php

namespace Database\Seeders\Place\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class PlaceTypesDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 27], ['slug' => 'place_types', 'name' => "Types de lieu"]);
        DictionnaryEntry::updateOrCreate(['id' => 68], ['dictionnary_id' => 27, 'name' => 'Centre de congrès']);
        DictionnaryEntry::updateOrCreate(['id' => 69], ['dictionnary_id' => 27, 'name' => 'Hôtel']);
        DictionnaryEntry::updateOrCreate(['id' => 70], ['dictionnary_id' => 27, 'name' => 'Université']);

    }
}
