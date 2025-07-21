<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_sellable_service_view AS
        select `s`.`id`                                                                                                  AS `id`,
       `s`.`deleted_at`                                                                                          AS `deleted_at`,
       `s`.`event_id`                                                                                            AS `event_id`,
       json_unquote(json_extract(`s`.`title`, '$.fr'))                                                           AS `title`,
       date_format(`s`.`service_date`, '%d/%m/%Y')                                                               AS `service_date`,
       `s`.`published`                                                                                           AS `published`,
       `s`.`pec_eligible`                                                                                        AS `pec_eligible`,
       group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator
                    ',<br>')                                                                                     AS `prices`,
       `sb`.`stock`                                                                                              AS `stock`,
       `sb`.`stock_label`                                                                                        AS `stock_label`,
       `sb`.`bookings_count`                                                                                     AS `bookings_count`,
       `sb`.`temp_bookings_count`                                                                                AS `temp_bookings_count`,
       `sb`.`temp_front_bookings_count`                                                                          AS `temp_front_bookings_count`,
       `sb`.`total_bookings_count`                                                                               AS `total_bookings_count`,
       `sb`.`available`                                                                                          AS `available`,
       `sb`.`available_label`                                                                                    AS `available_label`
from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp`
       on (`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb`
      on (`sb`.`id` = `s`.`id`))
group by `s`.`id`

        ",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_sellable_service_view AS
        select `s`.`id` AS `id`,`s`.`deleted_at` AS `deleted_at`,`s`.`event_id` AS `event_id`,json_unquote(json_extract(`s`.`title`,'$.fr')) AS `title`,`s`.`is_invitation` AS `is_invitation`,case when `s`.`is_invitation` = 1 then 'Oui' else 'Non' end AS `is_invitation_display`,json_unquote(json_extract(`sg`.`name`,'$.fr')) AS `group`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `service_date`,`s`.`published` AS `published`,`s`.`pec_eligible` AS `pec_eligible`,group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices`,`sb`.`stock` AS `stock`,`sb`.`stock_label` AS `stock_label`,`sb`.`bookings_count` AS `bookings_count`,`sb`.`temp_bookings_count` AS `temp_bookings_count`,`sb`.`temp_front_bookings_count` AS `temp_front_bookings_count`,`sb`.`total_bookings_count` AS `total_bookings_count`,`sb`.`available` AS `available`,`sb`.`available_label` AS `available_label` from (((`event_sellable_service` `s` left join `dictionnary_entries` `sg` on(`sg`.`id` = `s`.`service_group`)) left join `event_sellable_service_prices` `esp` on(`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb` on(`sb`.`id` = `s`.`id`)) group by `s`.`id`
        ",
        );
    }
};
