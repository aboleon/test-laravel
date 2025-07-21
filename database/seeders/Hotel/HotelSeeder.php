<?php

namespace Database\Seeders\Hotel;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hotel::updateOrCreate(['id' => 10], ['name' => "Grand Azure Resort"]);
        Hotel::updateOrCreate(['id' => 11], ['name' => "Hotel de la Plage"]);
        Hotel::updateOrCreate(['id' => 12], ['name' => "Albergo Bella Vista"]);
        Hotel::updateOrCreate(['id' => 13], ['name' => "Villa del Lago"]);
        Hotel::updateOrCreate(['id' => 14], ['name' => "Hotel du Parc"]);
        Hotel::updateOrCreate(['id' => 15], ['name' => "Emerald Bay Inn"]);
        Hotel::updateOrCreate(['id' => 16], ['name' => "Casa del Tramonto"]);
        Hotel::updateOrCreate(['id' => 17], ['name' => "Palazzo D'oro"]);
        Hotel::updateOrCreate(['id' => 18], ['name' => "Hotel du Soleil"]);
        Hotel::updateOrCreate(['id' => 19], ['name' => "Rifugio Romantico"]);
        Hotel::updateOrCreate(['id' => 20], ['name' => "Hôtel de la Riviera"]);
        Hotel::updateOrCreate(['id' => 21], ['name' => "Albergo della Rosa"]);
        Hotel::updateOrCreate(['id' => 22], ['name' => "Hotel des Vignes"]);
        Hotel::updateOrCreate(['id' => 23], ['name' => "Hôtel des Alpes"]);
        Hotel::updateOrCreate(['id' => 24], ['name' => "Albergo del Mare"]);
        Hotel::updateOrCreate(['id' => 25], ['name' => "Hôtel de la Vallée"]);
        Hotel::updateOrCreate(['id' => 26], ['name' => "Albergo della Luna"]);
        Hotel::updateOrCreate(['id' => 27], ['name' => "Albergo del Sole"]);
        Hotel::updateOrCreate(['id' => 28], ['name' => "Hôtel des Arts"]);
        Hotel::updateOrCreate(['id' => 29], ['name' => "Albergo delle Stelle"]);
        Hotel::updateOrCreate(['id' => 30], ['name' => "Hotel du Midi"]);

    }
}
