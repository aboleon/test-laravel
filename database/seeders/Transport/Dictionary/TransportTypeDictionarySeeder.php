<?php

namespace Database\Seeders\Transport\Dictionary;

use App\Models\Dictionnary;
use App\Models\DictionnaryEntry;
use Illuminate\Database\Seeder;

class TransportTypeDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Dictionnary::updateOrCreate(['id' => 6], ['slug' => 'transport', 'name' => "Type de transport"]);
        DictionnaryEntry::updateOrCreate(['id' => 57], ['dictionnary_id' => 6, 'name' => 'Avion']);
        DictionnaryEntry::updateOrCreate(['id' => 58], ['dictionnary_id' => 6, 'name' => 'Ferry']);

    }
}
