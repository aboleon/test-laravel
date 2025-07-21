<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_view AS
            SELECT
    `eg`.`id` AS `id`,
    `eg`.`deleted_at` AS `deleted_at`,
    `eg`.`event_id` AS `event_id`,
    json_unquote(json_extract(`eg`.`title`, '$.fr')) AS `title`,
    COALESCE(CONCAT(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A') AS `contact`,
    json_unquote(json_extract(`eg`.`comment`, '$.fr')) AS `comment`,
    `eg`.`amount_ht` AS `amount_ht`,
    `eg`.`amount_ht_used` AS `amount_ht_used`,
    `eg`.`amount_ht` - `eg`.`amount_ht_used` AS `amount_ht_remaining`,
    `eg`.`amount_ttc` AS `amount_ttc`,
    `eg`.`amount_ttc_used` AS `amount_ttc_used`,
    `eg`.`amount_ttc` - `eg`.`amount_ttc_used` AS `amount_ttc_remaining`,
    FORMAT(`eg`.`amount_ht` / 100.0, 2) AS `amount_ht_display`,
    FORMAT(`eg`.`amount_ht_used` / 100.0, 2) AS `amount_ht_used_display`,
    FORMAT((`eg`.`amount_ht` - `eg`.`amount_ht_used`) / 100.0, 2) AS `amount_ht_remaining_display`,
    FORMAT(`eg`.`amount_ttc` / 100.0, 2) AS `amount_ttc_display`,
    FORMAT(`eg`.`amount_ttc_used` / 100.0, 2) AS `amount_ttc_used_display`,
    FORMAT((`eg`.`amount_ttc` - `eg`.`amount_ttc_used`) / 100.0, 2) AS `amount_ttc_remaining_display`,
    `eg`.`pec_fee` AS `pec_fee`,
    FORMAT(`eg`.`pec_fee` / 100.0, 2) AS `pec_fee_display`,
    `eg`.`pax_avg` AS `pax_avg`,
    `eg`.`pax_max` AS `pax_max`,
    `eg`.`active` AS `active`,
    CASE
        WHEN EXISTS (SELECT 1 FROM `event_grant_funding_records` `fr` WHERE `fr`.`grant_id` = `eg`.`id` LIMIT 1)
        THEN 1
        ELSE 0
    END AS `is_grant_consumed`,
    (SELECT COUNT(*) FROM `event_grant_allocations` `ega` WHERE `ega`.`grant_id` = `eg`.`id`) AS `allocations`
FROM
    `event_grant` `eg`
    LEFT JOIN `event_grant_contact` `egc` ON (`eg`.`id` = `egc`.`grant_id`)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_view AS
            select `eg`.`id`                                                           AS `id`,
       `eg`.`deleted_at`                                                   AS `deleted_at`,
       `eg`.`event_id`                                                     AS `event_id`,
       json_unquote(json_extract(`eg`.`title`, '$.fr'))                    AS `title`,
       coalesce(concat(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A') AS `contact`,
       json_unquote(json_extract(`eg`.`comment`, '$.fr'))                  AS `comment`,
       `eg`.`amount_ht`                                                    AS `amount_ht`,
       `eg`.`amount_ht_used`                                               AS `amount_ht_used`,
       `eg`.`amount_ht` - `eg`.`amount_ht_used`                            AS `amount_ht_remaining`,
       `eg`.`amount_ttc`                                                   AS `amount_ttc`,
       `eg`.`amount_ttc_used`                                              AS `amount_ttc_used`,
       `eg`.`amount_ttc` - `eg`.`amount_ttc_used`                          AS `amount_ttc_remaining`,
       format(`eg`.`amount_ht` / 100.0, 2)                                 AS `amount_ht_display`,
       format(`eg`.`amount_ht_used` / 100.0, 2)                            AS `amount_ht_used_display`,
       format((`eg`.`amount_ht` - `eg`.`amount_ht_used`) / 100.0, 2)       AS `amount_ht_remaining_display`,
       format(`eg`.`amount_ttc` / 100.0, 2)                                AS `amount_ttc_display`,
       format(`eg`.`amount_ttc_used` / 100.0, 2)                           AS `amount_ttc_used_display`,
       format((`eg`.`amount_ttc` - `eg`.`amount_ttc_used`) / 100.0, 2)     AS `amount_ttc_remaining_display`,
       `eg`.`pec_fee`                                                      AS `pec_fee`,
       format(`eg`.`pec_fee` / 100.0, 2)                                   AS `pec_fee_display`,
       `eg`.`pax_avg`                                                      AS `pax_avg`,
       `eg`.`pax_max`                                                      AS `pax_max`,
       `eg`.`active`                                                       AS `active`,
       case
           when exists(select 1 from `event_grant_funding_records` `fr` where `fr`.`grant_id` = `eg`.`id` limit 1)
               then 1
           else 0 end                                                      AS `is_grant_consumed`
from (`event_grant` `eg` left join `event_grant_contact` `egc` on (`eg`.`id` = `egc`.`grant_id`))
        ");
    }

};
