<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE VIEW event_grant_stats AS

        SELECT
            pec_distribution.grant_id,
            CONCAT(users.first_name, ' ', users.last_name) AS participant,
            FORMAT(pec_distribution.total_net / 100.0, 2) AS total_net,
            FORMAT(pec_distribution.total_vat / 100.0, 2) AS total_vat,
            FORMAT((pec_distribution.total_net + pec_distribution.total_vat) / 100.0, 2) AS total,
            CASE
                WHEN pec_distribution.type = 'uncategorized' THEN 'Non catégorisé'
                WHEN pec_distribution.type = 'service' THEN 'Prestation'
                WHEN pec_distribution.type = 'accommodation' THEN 'Hebergement'
                WHEN pec_distribution.type = 'taxroom' THEN 'Frais de dossier hébergement'
                WHEN pec_distribution.type = 'transport' THEN 'Transport'
                WHEN pec_distribution.type = 'processing_fee' THEN 'Frais de dossier'
            END AS type,
            CASE
                WHEN pec_distribution.type = 'service' THEN JSON_UNQUOTE(JSON_EXTRACT(event_sellable_service.title, '$.fr'))
                WHEN pec_distribution.type IN ('accommodation', 'taxroom') THEN
                    CONCAT(
                        JSON_UNQUOTE(JSON_EXTRACT(dictionnary_entries.name, '$.fr')), ', ',
                        JSON_UNQUOTE(JSON_EXTRACT(event_accommodation_room_groups.name, '$.fr')), ', ',
                        hotels.name
                    )
            END AS shoppable
        FROM
            pec_distribution
            JOIN events_contacts ON pec_distribution.event_contact_id = events_contacts.id
            JOIN users ON events_contacts.user_id = users.id
            LEFT JOIN event_sellable_service ON pec_distribution.shoppable_id = event_sellable_service.id AND pec_distribution.type = 'service'
            LEFT JOIN event_accommodation_room ON pec_distribution.shoppable_id = event_accommodation_room.id AND pec_distribution.type IN ('accommodation', 'taxroom')
            LEFT JOIN event_accommodation_room_groups ON event_accommodation_room.room_group_id = event_accommodation_room_groups.id
            LEFT JOIN dictionnary_entries ON event_accommodation_room.room_id = dictionnary_entries.id
            LEFT JOIN event_accommodation ON event_accommodation_room_groups.event_accommodation_id = event_accommodation.id
            LEFT JOIN hotels ON event_accommodation.hotel_id = hotels.id


        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW event_grant_stats");
    }
};
