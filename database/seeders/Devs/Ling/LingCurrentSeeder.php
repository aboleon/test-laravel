<?php

namespace Database\Seeders\Devs\Ling;

use Database\Seeders\Account\AccountCardsSeeder;
use Database\Seeders\Account\AccountDocumentsSeeder;
use Database\Seeders\Account\AccountPhoneSeeder;
use Database\Seeders\Hotel\HotelAddressSeeder;
use Database\Seeders\Hotel\HotelSeeder;
use Database\Seeders\Order\EventContactOrderSeeder;
use Database\Seeders\ParticipationType\ParticipationTypeSeeder;
use Database\Seeders\Place\Dictionary\PlaceTypesDictionarySeeder;
use Database\Seeders\Place\PlaceAddressSeeder;
use Database\Seeders\Place\PlaceRoomSeeder;
use Database\Seeders\Place\PlaceSeeder;
use Database\Seeders\Program\Dictionary\InterventionTypeDictionarySeeder;
use Database\Seeders\Program\Dictionary\SellableServiceGroupDictionarySeeder;
use Database\Seeders\Program\Dictionary\SessionTypeDictionarySeeder;
use Database\Seeders\Program\EventContactInterventionSeeder;
use Database\Seeders\Program\EventProgramDaySeeder;
use Database\Seeders\Program\EventProgramInterventionSeeder;
use Database\Seeders\Program\EventProgramSessionSeeder;
use Database\Seeders\Sellable\EventSellableServiceSeeder;
use Database\Seeders\Transport\Dictionary\TransportStepDictionarySeeder;
use Database\Seeders\Transport\Dictionary\TransportTypeDictionarySeeder;
use Database\Seeders\Transport\TransportSeeder;
use Database\Seeders\User\EventContactSeeder;
use Database\Seeders\Vat\VatSeeder;
use Illuminate\Database\Seeder;


class
LingCurrentSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=Database\\Seeders\\Devs\\Ling\\LingCurrentSeeder
     *
     *
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->call([



            //
            ParticipationTypeSeeder::class,

            AccountCardsSeeder::class,
            AccountDocumentsSeeder::class,
            //
            PlaceTypesDictionarySeeder::class,
            PlaceSeeder::class,
            PlaceAddressSeeder::class,
            PlaceRoomSeeder::class,
            //
            HotelSeeder::class,
            HotelAddressSeeder::class,
            //
//            UserSeeder::class,
            AccountPhoneSeeder::class,
            EventContactSeeder::class,
            //
            SessionTypeDictionarySeeder::class,
            InterventionTypeDictionarySeeder::class,
            //
            EventProgramDaySeeder::class,
            EventProgramSessionSeeder::class,
            EventProgramInterventionSeeder::class,
            //
            TransportStepDictionarySeeder::class,
            TransportTypeDictionarySeeder::class,
            TransportSeeder::class,
            //
            EventContactInterventionSeeder::class,

            //
            VatSeeder::class,

            //
            SellableServiceGroupDictionarySeeder::class,
            EventSellableServiceSeeder::class,


            //
            EventContactOrderSeeder::class,


        ]);
    }
}
