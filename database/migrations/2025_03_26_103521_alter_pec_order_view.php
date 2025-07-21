<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW pec_order_view AS
        SELECT
    pd.id AS pec_distribution_id,
    pd.type AS pec_distribution_type,
    pd.grant_id AS grant_id,
    pd.order_id AS order_id,
    ec.event_id AS event_id,
    pd.event_contact_id AS event_contact_id,
    CONCAT(u.first_name, ' ', u.last_name) AS participant,

    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' THEN 0
                ELSE COALESCE(pd.total_net, 0)
            END
        ) / 100.0, 2
    ) AS total_net,

    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' THEN 0
                ELSE COALESCE(pd.total_vat, 0)
            END
        ) / 100.0, 2
    ) AS total_vat,

    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' THEN COALESCE(pd.unit_price, 0)
                ELSE COALESCE(pd.total_net, 0) + COALESCE(pd.total_vat, 0)
            END
        ) / 100.0, 2
    ) AS total,

    CASE
        WHEN pd.type = 'transport' THEN 'Transport'
        WHEN pd.type = 'service' THEN 'Prestation'
        WHEN pd.type = 'accommodation' THEN 'Hebergement'
        ELSE 'Other'
    END AS type,

    CASE
        WHEN pd.type = 'service' THEN JSON_UNQUOTE(JSON_EXTRACT(ess.title, '$.fr'))
        WHEN pd.type IN ('accommodation', 'taxroom') THEN
            GROUP_CONCAT(DISTINCT CONCAT(
                JSON_UNQUOTE(JSON_EXTRACT(de.name, '$.fr')), ', ',
                JSON_UNQUOTE(JSON_EXTRACT(eagr.name, '$.fr')), ', ',
                h.name
            ) SEPARATOR ', ')
        WHEN pd.type = 'transport' THEN
            CASE
                WHEN pd.shoppable_id = 1 THEN 'Divine Id'
                WHEN pd.shoppable_id = 2 THEN 'Participant'
                WHEN pd.shoppable_id = 3 THEN 'Non demandé'
                ELSE NULL
            END
    END AS shoppable,

    JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country,
    JSON_UNQUOTE(JSON_EXTRACT(de_domain.name, '$.fr')) AS domain,
    JSON_UNQUOTE(JSON_EXTRACT(eg.title, '$.fr')) AS `grant`

FROM pec_distribution pd
LEFT JOIN events_contacts ec ON pd.event_contact_id = ec.id
LEFT JOIN users u ON ec.user_id = u.id
LEFT JOIN event_sellable_service ess
    ON pd.shoppable_id = ess.id AND pd.type = 'service'
LEFT JOIN event_accommodation_room ear
    ON pd.shoppable_id = ear.id AND pd.type IN ('accommodation', 'taxroom')
LEFT JOIN event_accommodation_room_groups eagr
    ON ear.room_group_id = eagr.id
LEFT JOIN dictionnary_entries de
    ON ear.room_id = de.id
LEFT JOIN event_accommodation ea
    ON eagr.event_accommodation_id = ea.id
LEFT JOIN hotels h
    ON ea.hotel_id = h.id
LEFT JOIN event_grant eg
    ON pd.grant_id = eg.id
LEFT JOIN (
    SELECT DISTINCT
        account_address.user_id,
        account_address.country_code
    FROM account_address
) aa ON u.id = aa.user_id
LEFT JOIN countries c
    ON aa.country_code = c.code
LEFT JOIN (
    SELECT DISTINCT
        user_id,
        domain_id
    FROM account_profile
) ap ON u.id = ap.user_id
LEFT JOIN dictionnary_entries de_domain
    ON ap.domain_id = de_domain.id

GROUP BY
    pd.id,
    pd.type,
    pd.grant_id,
    pd.order_id,
    ec.event_id,
    CONCAT(u.first_name, ' ', u.last_name),
    pd.shoppable_id

HAVING SUM(
    CASE
        WHEN pd.type = 'transport' THEN COALESCE(pd.unit_price, 0)
        ELSE COALESCE(pd.total_net, 0) + COALESCE(pd.total_vat, 0)
    END
) <> 0
",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW pec_order_view AS
        select `pd`.`id` AS `pec_distribution_id`,`pd`.`type` AS `pec_distribution_type`,`pd`.`grant_id` AS `grant_id`,`pd`.`order_id` AS `order_id`,`o`.`event_id` AS `event_id`,`pd`.`event_contact_id` AS `event_contact_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `participant`,format(sum(distinct `pd`.`total_net`) / 100.0,2) AS `total_net`,format(sum(distinct `pd`.`total_vat`) / 100.0,2) AS `total_vat`,format(sum(distinct `pd`.`total_net` + `pd`.`total_vat`) / 100.0,2) AS `total`,case when `pd`.`type` = 'uncategorized' then 'Non catégorisé' when `pd`.`type` = 'service' then 'Prestation' when `pd`.`type` = 'accommodation' then 'Hebergement' when `pd`.`type` = 'taxroom' then 'Frais de dossier hébergement' when `pd`.`type` = 'transport' then 'Transport' when `pd`.`type` = 'processing_fee' then 'Frais de dossier' end AS `type`,case when `pd`.`type` = 'service' then json_unquote(json_extract(`ess`.`title`,'$.fr')) when `pd`.`type` in ('accommodation','taxroom') then (select group_concat(distinct concat(json_unquote(json_extract(`de`.`name`,'$.fr')),', ',json_unquote(json_extract(`eagr`.`name`,'$.fr')),', ',`h`.`name`) separator ',') from ((((`event_accommodation_room` `ear` left join `event_accommodation_room_groups` `eagr` on(`ear`.`room_group_id` = `eagr`.`id`)) left join `dictionnary_entries` `de` on(`ear`.`room_id` = `de`.`id`)) left join `event_accommodation` `ea` on(`eagr`.`event_accommodation_id` = `ea`.`id`)) left join `hotels` `h` on(`ea`.`hotel_id` = `h`.`id`)) where `pd`.`shoppable_id` = `ear`.`id`) end AS `shoppable`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de_domain`.`name`,'$.fr')) AS `domain`,json_unquote(json_extract(`eg`.`title`,'$.fr')) AS `grant` from (((((((((`pec_distribution` `pd` join `orders` `o` on(`pd`.`order_id` = `o`.`id`)) join `events_contacts` `ec` on(`pd`.`event_contact_id` = `ec`.`id`)) join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join (select distinct `event_sellable_service`.`id` AS `id`,`event_sellable_service`.`title` AS `title` from `event_sellable_service`) `ess` on(`pd`.`shoppable_id` = `ess`.`id` and `pd`.`type` = 'service')) left join (select distinct `account_address`.`user_id` AS `user_id`,`account_address`.`country_code` AS `country_code` from `account_address`) `aa` on(`u`.`id` = `aa`.`user_id`)) left join `countries` `c` on(`aa`.`country_code` = `c`.`code`)) left join (select distinct `account_profile`.`user_id` AS `user_id`,`account_profile`.`domain_id` AS `domain_id` from `account_profile`) `ap` on(`u`.`id` = `ap`.`user_id`)) left join `dictionnary_entries` `de_domain` on(`ap`.`domain_id` = `de_domain`.`id`)) left join `event_grant` `eg` on(`pd`.`grant_id` = `eg`.`id`)) group by `pd`.`grant_id`,`pd`.`order_id`,`o`.`event_id`,concat(`u`.`first_name`,' ',`u`.`last_name`),`pd`.`type`,`ess`.`title`,json_unquote(json_extract(`c`.`name`,'$.fr')),json_unquote(json_extract(`de_domain`.`name`,'$.fr')),json_unquote(json_extract(`eg`.`title`,'$.fr')) having sum(`pd`.`total_net`) <> 0",
        );
    }
};
