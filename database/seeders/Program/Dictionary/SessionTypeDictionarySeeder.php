<?php

namespace Database\Seeders\Program\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class SessionTypeDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 24], ['slug' => 'session_types', 'name' => "Type Session"]);
        DictionnaryEntry::updateOrCreate(['id' => 60], ['dictionnary_id' => 24, 'name' => 'Lunch']);
        DictionnaryEntry::updateOrCreate(['id' => 61], ['dictionnary_id' => 24, 'name' => 'Atelier']);
        DictionnaryEntry::updateOrCreate(['id' => 62], ['dictionnary_id' => 24, 'name' => 'Conférence']);

    }
}
