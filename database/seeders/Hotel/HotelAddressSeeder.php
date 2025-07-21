<?php

namespace Database\Seeders\Hotel;

use App\Models\HotelAddress;
use Illuminate\Database\Seeder;

class HotelAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HotelAddress::updateOrCreate(['id' => 10], ['hotel_id' => 10, "locality" => "Lyon", "country_code" => "FR", "text_address" => "1 rue de la Paix"]);
        HotelAddress::updateOrCreate(['id' => 11], ['hotel_id' => 11, "locality" => "Marseille", "country_code" => "FR", "text_address" => "15 Avenue des Goudes"]);
        HotelAddress::updateOrCreate(['id' => 12], ['hotel_id' => 12, "locality" => "Rome", "country_code" => "IT", "text_address" => "23 Via Roma"]);
        HotelAddress::updateOrCreate(['id' => 13], ['hotel_id' => 13, "locality" => "Venice", "country_code" => "IT", "text_address" => "46 Canal Grande"]);
        HotelAddress::updateOrCreate(['id' => 14], ['hotel_id' => 14, "locality" => "Paris", "country_code" => "FR", "text_address" => "89 Boulevard Saint-Germain"]);
        HotelAddress::updateOrCreate(['id' => 15], ['hotel_id' => 15, "locality" => "Lyon", "country_code" => "FR", "text_address" => "32 Place Bellecour"]);
        HotelAddress::updateOrCreate(['id' => 16], ['hotel_id' => 16, "locality" => "Milan", "country_code" => "IT", "text_address" => "64 Via Montenapoleone"]);
        HotelAddress::updateOrCreate(['id' => 17], ['hotel_id' => 17, "locality" => "Rome", "country_code" => "IT", "text_address" => "28 Piazza della Signoria"]);
        HotelAddress::updateOrCreate(['id' => 18], ['hotel_id' => 18, "locality" => "Bordeaux", "country_code" => "FR", "text_address" => "52 Cours de l'Intendance"]);
        HotelAddress::updateOrCreate(['id' => 19], ['hotel_id' => 19, "locality" => "Venice", "country_code" => "IT", "text_address" => "19 Via Toledo"]);
        HotelAddress::updateOrCreate(['id' => 20], ['hotel_id' => 20, "locality" => "Marseille", "country_code" => "FR", "text_address" => "77 Promenade des Anglais"]);
        HotelAddress::updateOrCreate(['id' => 21], ['hotel_id' => 21, "locality" => "Rome", "country_code" => "IT", "text_address" => "50 Via del Corso"]);
        HotelAddress::updateOrCreate(['id' => 22], ['hotel_id' => 22, "locality" => "Paris", "country_code" => "FR", "text_address" => "95 Rue de Rivoli"]);
        HotelAddress::updateOrCreate(['id' => 23], ['hotel_id' => 23, "locality" => "Marseille", "country_code" => "FR", "text_address" => "100 Boulevard Baille"]);
        HotelAddress::updateOrCreate(['id' => 24], ['hotel_id' => 24, "locality" => "Venice", "country_code" => "IT", "text_address" => "88 Fondamenta Santa Lucia"]);
        HotelAddress::updateOrCreate(['id' => 25], ['hotel_id' => 25, "locality" => "Lyon", "country_code" => "FR", "text_address" => "45 Rue de la République"]);
        HotelAddress::updateOrCreate(['id' => 26], ['hotel_id' => 26, "locality" => "Milan", "country_code" => "IT", "text_address" => "72 Corso Buenos Aires"]);
        HotelAddress::updateOrCreate(['id' => 27], ['hotel_id' => 27, "locality" => "Florence", "country_code" => "IT", "text_address" => "39 Via de' Tornabuoni"]);
        HotelAddress::updateOrCreate(['id' => 28], ['hotel_id' => 28, "locality" => "Lyon", "country_code" => "FR", "text_address" => "88 Cours Alsace Lorraine"]);
        HotelAddress::updateOrCreate(['id' => 29], ['hotel_id' => 29, "locality" => "Venice", "country_code" => "IT", "text_address" => "44 Via Chiaia"]);
        HotelAddress::updateOrCreate(['id' => 30], ['hotel_id' => 30, "locality" => "Paris", "country_code" => "FR", "text_address" => "120 Rue de France"]);


    }
}
