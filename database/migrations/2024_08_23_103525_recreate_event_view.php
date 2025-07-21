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

        DB::statement("CREATE OR REPLACE VIEW event_view AS
SELECT `e`.`id`                                                                                          AS `id`,
       `e`.`deleted_at`                                                                                  AS `deleted_at`,
       `e`.`bank_card_code`                                                                              AS `codecb`,
       DATE_FORMAT(`e`.`starts`, '%d/%m/%Y')                                                             AS `starts`,
       DATE_FORMAT(`e`.`ends`, '%d/%m/%Y')                                                               AS `ends`,
       JSON_UNQUOTE(JSON_EXTRACT(`et`.`name`, '$.fr'))                                                   AS `name`,
       CASE
           WHEN JSON_UNQUOTE(JSON_EXTRACT(`et`.`subname`, '$.fr')) = 'null' THEN ''
           ELSE JSON_UNQUOTE(JSON_EXTRACT(`et`.`subname`, '$.fr'))
       END                                                                                               AS `subname`,
       JSON_UNQUOTE(JSON_EXTRACT(`d`.`name`, '$.fr'))                                                    AS `parent`,
       JSON_UNQUOTE(JSON_EXTRACT(`d2`.`name`, '$.fr'))                                                   AS `type`,
       CONCAT_WS(' ', `u`.`first_name`, `u`.`last_name`)                                                 AS `admin`,
       CASE
           WHEN `e`.`published` = 1 THEN 'Oui'
           WHEN `e`.`published` IS NULL THEN 'Non'
           ELSE 'other'
       END                                                                                               AS `published`,
       COUNT(`ec`.`id`)                                                                                  AS `participants_count`
FROM `events` `e`
LEFT JOIN `events_texts` `et` ON `e`.`id` = `et`.`event_id`
LEFT JOIN `users` `u` ON `e`.`admin_id` = `u`.`id`
LEFT JOIN `dictionnary_entries` `d` ON `e`.`event_main_id` = `d`.`id`
LEFT JOIN `dictionnary_entries` `d2` ON `e`.`event_type_id` = `d2`.`id`
LEFT JOIN `events_contacts` `ec` ON `e`.`id` = `ec`.`event_id`
GROUP BY `e`.`id`

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_view AS
select `e`.`id` AS `id`,`e`.`deleted_at` AS `deleted_at`,`e`.`bank_card_code` AS `codecb`,date_format(`e`.`starts`,'%d/%m/%Y') AS `starts`,date_format(`e`.`ends`,'%d/%m/%Y') AS `ends`,json_unquote(json_extract(`et`.`name`,'$.fr')) AS `name`,json_unquote(json_extract(`et`.`subname`,'$.fr')) AS `subname`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `parent`,json_unquote(json_extract(`d2`.`name`,'$.fr')) AS `type`,concat_ws(' ',`u`.`first_name`,`u`.`last_name`) AS `admin`,case when `e`.`published` = 1 then 'Oui' when `e`.`published` is null then 'Non' else 'other' end AS `published` from ((((`events` `e` left join `events_texts` `et` on(`e`.`id` = `et`.`event_id`)) left join `users` `u` on(`e`.`admin_id` = `u`.`id`)) left join `dictionnary_entries` `d` on(`e`.`event_main_id` = `d`.`id`)) left join `dictionnary_entries` `d2` on(`e`.`event_type_id` = `d2`.`id`))
        ");
    }
};
