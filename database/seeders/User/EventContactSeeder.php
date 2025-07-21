<?php

namespace Database\Seeders\User;

use App\Enum\DesiredTransportManagement;
use App\Models\EventContact;
use Illuminate\Database\Seeder;

class EventContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventContact::updateOrCreate(["id" => 10], [
            'event_id' => 1,
            'user_id' => 10,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 11], [
            'event_id' => 1,
            'user_id' => 11,
            'participation_type_id' => 2,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 12], [
            'event_id' => 1,
            'user_id' => 12,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 13], [
            'event_id' => 1,
            'user_id' => 13,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 14], [
            'event_id' => 1,
            'user_id' => 14,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 15], [
            'event_id' => 1,
            'user_id' => 15,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 16], [
            'event_id' => 1,
            'user_id' => 16,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 17], [
            'event_id' => 1,
            'user_id' => 17,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 18], [
            'event_id' => 1,
            'user_id' => 18,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 19], [
            'event_id' => 1,
            'user_id' => 19,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 20], [
            'event_id' => 1,
            'user_id' => 20,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 21], [
            'event_id' => 1,
            'user_id' => 21,
            'participation_type_id' => 4,
            'registration_type' => null,
        ]);
        EventContact::updateOrCreate(["id" => 22], [
            'event_id' => 1,
            'user_id' => 22,
            'participation_type_id' => 2,
            'registration_type' => null,
        ]);
    }
}
