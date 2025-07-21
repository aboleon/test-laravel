<?php

namespace Database\Seeders\Transport;

use App\Models\EventManager\Transport\EventTransport;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        EventTransport::updateOrCreate(['id' => 1], [
            'events_contacts_id' => 10,
            'departure_online' => null,
            'departure_step' => 55,
            'departure_transport_type' => 57,
            'departure_start_date' => '2023-09-28',
            'departure_start_time' => '09:00:00',
            'departure_start_location' => 'Marseille',
            'departure_end_time' => '10:45:00',
            'departure_end_location' => "Paris CDG",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => null,
            'return_step' => 55,
            'return_transport_type' => 57,
            'return_start_date' => '2023-09-30',
            'return_start_time' => '16:54:00',
            'return_start_location' => "Paris CDG",
            'return_end_time' => '19:21:00',
            'return_end_location' => 'Marseille',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => '10:53:00',
            'transfer_info' => "It's gonna rain, take an umbrella",
            'travel_preferences' => "I hate cats",
            'price_before_tax' => 10000,
            'price_after_tax' => 15000,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 2], [
            'events_contacts_id' => 11,
            'departure_online' => null,
            'departure_step' => 56,
            'departure_transport_type' => 57,
            'departure_start_date' => '2023-09-28',
            'departure_start_time' => '08:45:00',
            'departure_start_location' => 'Nice',
            'departure_end_time' => '10:45:00',
            'departure_end_location' => "Paris CDG",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => 1,
            'return_step' => 56,
            'return_transport_type' => 57,
            'return_start_date' => '2023-09-30',
            'return_start_time' => '17:21:00',
            'return_start_location' => "Paris CDG",
            'return_end_time' => '19:21:00',
            'return_end_location' => 'Nice',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => null,
            'transfer_info' => "It's gonna rain, take an umbrella",
            'travel_preferences' => "",
            'price_before_tax' => 20000,
            'price_after_tax' => 25000,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 3], [
            'events_contacts_id' => 12,
            'departure_online' => null,
            'departure_step' => 56,
            'departure_transport_type' => 57,
            'departure_start_date' => '2023-10-04',
            'departure_start_time' => '08:32:00',
            'departure_start_location' => 'Aurillac',
            'departure_end_time' => '10:32:00',
            'departure_end_location' => "Saint-Georges",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => 1,
            'return_step' => 56,
            'return_transport_type' => 57,
            'return_start_date' => '2023-10-05',
            'return_start_time' => '14:42:00',
            'return_start_location' => "Saint-Georges les bains",
            'return_end_time' => '16:42:00',
            'return_end_location' => 'Aurillac',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => '10:51:00',
            'transfer_info' => null,
            'travel_preferences' => "",
            'price_before_tax' => 20000,
            'price_after_tax' => 25000,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 4], [
            'events_contacts_id' => 13,
            'departure_online' => 1,
            'departure_step' => 55,
            'departure_transport_type' => 58,
            'departure_start_date' => '2023-11-24',
            'departure_start_time' => '08:32:00',
            'departure_start_location' => 'Port de Marseille',
            'departure_end_time' => '14:45:00',
            'departure_end_location' => "Naples",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => null,
            'return_step' => 55,
            'return_transport_type' => 58,
            'return_start_date' => '2023-11-27',
            'return_start_time' => '20:20:00',
            'return_start_location' => "Naples",
            'return_end_date' => '2023-11-28',
            'return_end_time' => '02:21:00',
            'return_end_location' => 'Port de Marseille',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => '15:00:00',
            'transfer_info' => null,
            'travel_preferences' => "",
            'price_before_tax' => 20000,
            'price_after_tax' => 25000,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 5], [
            'events_contacts_id' => 14,
            'departure_online' => 1,
            'departure_step' => 55,
            'departure_transport_type' => 57,
            'departure_start_date' => '2023-12-05',
            'departure_start_time' => '05:00:00',
            'departure_start_location' => 'Clermont-Ferrand',
            'departure_end_time' => '06:45:00',
            'departure_end_location' => "Paris CDG",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => 1,
            'return_step' => 55,
            'return_transport_type' => 57,
            'return_start_date' => '2023-12-06',
            'return_start_time' => '08:00:00',
            'return_start_location' => "Paris CDG",
            'return_end_time' => '09:45:00',
            'return_end_location' => 'Clermont-Ferrand',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => null,
            'transfer_info' => null,
            'travel_preferences' => "",
            'price_before_tax' => 20000,
            'price_after_tax' => 25000,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 6], [
            'events_contacts_id' => 15,
            'departure_online' => null,
            'departure_step' => 55,
            'departure_transport_type' => 57,
            'departure_start_date' => '2023-12-20',
            'departure_start_time' => '06:00:00',
            'departure_start_location' => 'Rungis',
            'departure_end_time' => '07:45:00',
            'departure_end_location' => "Paris CDG",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => null,
            'return_step' => 55,
            'return_transport_type' => 57,
            'return_start_date' => '2023-12-21',
            'return_start_time' => '08:00:00',
            'return_start_location' => "Paris CDG",
            'return_end_time' => '09:45:00',
            'return_end_location' => 'Rungis',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => "08:05:00",
            'transfer_info' => null,
            'travel_preferences' => "",
            'price_before_tax' => 10000,
            'price_after_tax' => 15254,
            'max_reimbursement' => 13000,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 7], [
            'events_contacts_id' => 16,
            'departure_online' => null,
            'departure_step' => null,
            'departure_transport_type' => null,
            'departure_start_date' => null,
            'departure_start_time' => null,
            'departure_start_location' => null,
            'departure_end_location' => null,
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => null,
            'return_step' => null,
            'return_transport_type' => null,
            'return_start_date' => null,
            'return_start_time' => null,
            'return_start_location' => null,
            'return_end_time' => '19:21:00',
            'return_end_location' => null,
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => null,
            'transfer_info' => null,
            'travel_preferences' => null,
            'price_before_tax' => null,
            'price_after_tax' => null,
            'max_reimbursement' => null,
            'admin_comment' => null,
        ]);

        EventTransport::updateOrCreate(['id' => 8], [
            'events_contacts_id' => 17,
            'departure_online' => null,
            'departure_step' => 55,
            'departure_transport_type' => 57,
            'departure_start_date' => '2024-01-10',
            'departure_start_time' => '06:00:00',
            'departure_start_location' => 'Lyon',
            'departure_end_time' => '07:45:00',
            'departure_end_location' => "Paris Roissy",
            'departure_reference_info_participant' => null,
            'departure_participant_comment' => null,
            //
            'return_online' => null,
            'return_step' => 55,
            'return_transport_type' => 57,
            'return_start_date' => '2024-01-12',
            'return_start_time' => '08:00:00',
            'return_start_location' => "Paris Roissy",
            'return_end_time' => '09:45:00',
            'return_end_location' => 'Lyon',
            'return_reference_info_participant' => null,
            'return_participant_comment' => null,
            //
            'transfer_shuttle_time' => "08:05:00",
            'transfer_info' => null,
            'travel_preferences' => "",
            'price_before_tax' => 15500,
            'price_after_tax' => 15945,
            'max_reimbursement' => 12000,
            'admin_comment' => null,
        ]);
    }
}
