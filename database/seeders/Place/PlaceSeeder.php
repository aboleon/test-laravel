<?php

namespace Database\Seeders\Place;

use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Place::updateOrCreate(['id' => 10], ['name' => "Salle de Bal Riviera", 'email' => 'riveira@gmail.com', "place_type_id" => 68]);
        Place::updateOrCreate(['id' => 11], ['name' => "Auditorium Larkspur", 'email' => 'larkspur@gmail.com', "place_type_id" => 68]);
        Place::updateOrCreate(['id' => 12], ['name' => "Salon Bella Vue sur le Toit", 'email' => 'bellavue@gmail.com', "place_type_id" => 69]);
        Place::updateOrCreate(['id' => 13], ['name' => "Amphithéâtre du Jardin Willowbrook", 'email' => 'willowbrook@gmail.com', "place_type_id" => 69]);
        Place::updateOrCreate(['id' => 14], ['name' => "Salle de Conférence Stellaire", 'email' => 'stellar@gmail.com', "place_type_id" => 70]);
    }
}
