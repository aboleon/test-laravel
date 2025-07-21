<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_groups_view AS
            SELECT
    `eg`.`id` AS `id`,
    `g`.`id` AS `group_id`,
    `g`.`name` AS `group_name`,
    `g`.`company` AS `group_company`,
    CAST(`eg`.`created_at` AS DATE) AS `event_group_created_at`,
    `eg`.`event_id` AS `event_id`,
    `eg`.`comment` AS `comment`,
    `u`.`id` AS `user_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `main_contact_name`,
    `u`.`email` AS `main_contact_email`,
    `p`.`phone` AS `main_contact_phone`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `main_contact_country`,
    (
        SELECT COUNT(DISTINCT `ec`.`id`)
        FROM `events_contacts` `ec`
        JOIN `event_group_contacts` `egc` ON `ec`.`user_id` = `egc`.`user_id`
        WHERE `egc`.`event_group_id` = `eg`.`id`
          AND `ec`.`event_id` = `eg`.`event_id`
    ) AS `participants_count`,
    (
        SELECT COUNT(*)
        FROM `orders`
        WHERE `client_type` = 'group'
          AND `client_id` = `g`.`id`
    ) AS `orders_count`
FROM `groups` `g`
JOIN `event_groups` `eg` ON `g`.`id` = `eg`.`group_id`
LEFT JOIN `users` `u` ON `eg`.`main_contact_id` = `u`.`id`
LEFT JOIN `main_account_phone_view` `p` ON `u`.`id` = `p`.`user_id`
LEFT JOIN `main_account_address_view` `a` ON `u`.`id` = `a`.`user_id`
LEFT JOIN `countries` `c` ON `a`.`country_code` = `c`.`code`
WHERE `g`.`deleted_at` IS NULL;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_groups_view AS
            select `eg`.`id` AS `id`,`g`.`id` AS `group_id`,`g`.`name` AS `group_name`,`g`.`company` AS `group_company`,cast(`eg`.`created_at` as date) AS `event_group_created_at`,`eg`.`event_id` AS `event_id`,`eg`.`comment` AS `comment`,`u`.`id` AS `user_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `main_contact_name`,`u`.`email` AS `main_contact_email`,`p`.`phone` AS `main_contact_phone`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `main_contact_country`,(select count(distinct `ec`.`id`) from (`events_contacts` `ec` join `event_group_contacts` `egc` on((`ec`.`user_id` = `egc`.`user_id`))) where ((`egc`.`event_group_id` = `eg`.`id`) and (`ec`.`event_id` = `eg`.`event_id`))) AS `participants_count` from (((((`groups` `g` join `event_groups` `eg` on((`g`.`id` = `eg`.`group_id`))) left join `users` `u` on((`eg`.`main_contact_id` = `u`.`id`))) left join `main_account_phone_view` `p` on((`u`.`id` = `p`.`user_id`))) left join `main_account_address_view` `a` on((`u`.`id` = `a`.`user_id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) where (`g`.`deleted_at` is null)
        ");
    }
};
