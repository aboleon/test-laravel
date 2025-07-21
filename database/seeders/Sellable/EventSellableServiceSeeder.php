<?php

namespace Database\Seeders\Sellable;


use App\Models\EventManager\Sellable as SellableService;
use App\Models\EventManager\Sellable\Deposit;
use App\Models\EventManager\Sellable\Price;
use App\Models\EventManager\Sellable\SellableServiceParticipationType;
use Illuminate\Database\Seeder;

class EventSellableServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //--------------------------------------------
        // Badges
        //--------------------------------------------
        SellableService::updateOrCreate(["id" => 1], [
            'event_id' => 1,
            'published' => 1,
            'service_group' => 21,
            'place_id' => 10,
            'stock' => 5000,
            'pec_eligible' => 1,
            'pec_max_pax' => 1,
            'vat_id' => 1,
            'title' => "Badge avec détecteur de présence",
        ]);

        Deposit::updateOrCreate(["id" => 1], [
            'event_sellable_service_id' => 1,
            'amount' => 0,
        ]);

        SellableServiceParticipationType::updateOrCreate(
            [
                "event_sellable_service_id" => 1,
                "participation_id" => 4,
            ],
            [
                'event_sellable_service_id' => 1,
                'participation_id' => 4,
            ],
        );

        Price::updateOrCreate(["id" => 1], [
            "event_sellable_service_id" => 1,
            "price" => 12,
        ]);


        //--------------------------------------------
        // Ateliers
        //--------------------------------------------
        SellableService::updateOrCreate(["id" => 2], [
            'event_id' => 1,
            'published' => 1,
            'service_group' => 22,
            'place_id' => 10,
            'stock' => 10,
            'pec_eligible' => 1,
            'pec_max_pax' => 1,
            'vat_id' => 1,
            'title' => "Atelier de test sur cobaye vivant",
        ]);

        Deposit::updateOrCreate(["id" => 2], [
            'event_sellable_service_id' => 2,
            'amount' => 300,
        ]);

        SellableServiceParticipationType::updateOrCreate(
            [
                "event_sellable_service_id" => 2,
                "participation_id" => 4,
            ],
            [
                'event_sellable_service_id' => 2,
                'participation_id' => 4,
            ],
        );

        Price::updateOrCreate(["id" => 2], [
            "event_sellable_service_id" => 2,
            "price" => 1200,
            'starts' => '11/10/2023 10:00',
            'ends' => '12/10/2023 11:00',
        ]);

        Price::updateOrCreate(["id" => 3], [
            "event_sellable_service_id" => 2,
            "price" => 1300,
            'starts' => '12/10/2023 10:00',
            'ends' => '13/10/2023 11:00',
        ]);

        //--------------------------------------------
        // Dîners
        //--------------------------------------------
        SellableService::updateOrCreate(["id" => 3], [
            'event_id' => 1,
            'published' => 1,
            'service_group' => 24,
            'place_id' => 10,
            'stock' => 3,
            'pec_eligible' => 1,
            'pec_max_pax' => 1,
            'vat_id' => 1,
            'title' => "Dîners avec le fondateur de la société",
        ]);

        Deposit::updateOrCreate(["id" => 3], [
            'event_sellable_service_id' => 3,
            'amount' => 0,
        ]);

        SellableServiceParticipationType::updateOrCreate(
            [
                "event_sellable_service_id" => 3,
                "participation_id" => 4,
            ],
            [
                'event_sellable_service_id' => 3,
                'participation_id' => 4,
            ],
        );

        Price::updateOrCreate(["id" => 4], [
            "event_sellable_service_id" => 3,
            "price" => 370,
        ]);

    }
}
