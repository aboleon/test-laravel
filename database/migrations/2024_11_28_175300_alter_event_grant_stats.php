<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_stats AS
        SELECT
    pd.grant_id AS grant_id,
    pd.order_id AS order_id,
    o.event_id AS event_id,
    CONCAT(u.first_name, ' ', u.last_name) AS participant,
    FORMAT(pd.total_net / 100.0, 2) AS total_net,
    FORMAT(pd.total_vat / 100.0, 2) AS total_vat,
    FORMAT((pd.total_net + pd.total_vat) / 100.0, 2) AS total,
    CASE
        WHEN pd.type = 'uncategorized' THEN 'Non catégorisé'
        WHEN pd.type = 'service' THEN 'Prestation'
        WHEN pd.type = 'accommodation' THEN 'Hebergement'
        WHEN pd.type = 'taxroom' THEN 'Frais de dossier hébergement'
        WHEN pd.type = 'transport' THEN 'Transport'
        WHEN pd.type = 'processing_fee' THEN 'Frais de dossier'
    END AS type,
    CASE
        WHEN pd.type = 'service' THEN JSON_UNQUOTE(JSON_EXTRACT(ess.title, '$.fr'))
        WHEN pd.type IN ('accommodation', 'taxroom') THEN CONCAT(
            JSON_UNQUOTE(JSON_EXTRACT(de.name, '$.fr')), ', ',
            JSON_UNQUOTE(JSON_EXTRACT(eagr.name, '$.fr')), ', ',
            h.name
        )
    END AS shoppable
FROM pec_distribution pd
JOIN orders o ON pd.order_id = o.id
JOIN events_contacts ec ON pd.event_contact_id = ec.id
JOIN users u ON ec.user_id = u.id
LEFT JOIN event_sellable_service ess ON pd.shoppable_id = ess.id AND pd.type = 'service'
LEFT JOIN event_accommodation_room ear ON pd.shoppable_id = ear.id AND pd.type IN ('accommodation', 'taxroom')
LEFT JOIN event_accommodation_room_groups eagr ON ear.room_group_id = eagr.id
LEFT JOIN dictionnary_entries de ON ear.room_id = de.id
LEFT JOIN event_accommodation ea ON eagr.event_accommodation_id = ea.id
LEFT JOIN hotels h ON ea.hotel_id = h.id");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_stats AS
        select `pec_distribution`.`grant_id` AS `grant_id`,concat(`users`.`first_name`,' ',`users`.`last_name`) AS `participant`,format(`pec_distribution`.`total_net` / 100.0,2) AS `total_net`,format(`pec_distribution`.`total_vat` / 100.0,2) AS `total_vat`,format((`pec_distribution`.`total_net` + `pec_distribution`.`total_vat`) / 100.0,2) AS `total`,case when `pec_distribution`.`type` = 'uncategorized' then 'Non catégorisé' when `pec_distribution`.`type` = 'service' then 'Prestation' when `pec_distribution`.`type` = 'accommodation' then 'Hebergement' when `pec_distribution`.`type` = 'taxroom' then 'Frais de dossier hébergement' when `pec_distribution`.`type` = 'transport' then 'Transport' when `pec_distribution`.`type` = 'processing_fee' then 'Frais de dossier' end AS `type`,case when `pec_distribution`.`type` = 'service' then json_unquote(json_extract(`event_sellable_service`.`title`,'$.fr')) when `pec_distribution`.`type` in ('accommodation','taxroom') then concat(json_unquote(json_extract(`dictionnary_entries`.`name`,'$.fr')),', ',json_unquote(json_extract(`event_accommodation_room_groups`.`name`,'$.fr')),', ',`hotels`.`name`) end AS `shoppable` from ((((((((`pec_distribution` join `events_contacts` on(`pec_distribution`.`event_contact_id` = `events_contacts`.`id`)) join `users` on(`events_contacts`.`user_id` = `users`.`id`)) left join `event_sellable_service` on(`pec_distribution`.`shoppable_id` = `event_sellable_service`.`id` and `pec_distribution`.`type` = 'service')) left join `event_accommodation_room` on(`pec_distribution`.`shoppable_id` = `event_accommodation_room`.`id` and `pec_distribution`.`type` in ('accommodation','taxroom'))) left join `event_accommodation_room_groups` on(`event_accommodation_room`.`room_group_id` = `event_accommodation_room_groups`.`id`)) left join `dictionnary_entries` on(`event_accommodation_room`.`room_id` = `dictionnary_entries`.`id`)) left join `event_accommodation` on(`event_accommodation_room_groups`.`event_accommodation_id` = `event_accommodation`.`id`)) left join `hotels` on(`event_accommodation`.`hotel_id` = `hotels`.`id`)) ");
    }
};
