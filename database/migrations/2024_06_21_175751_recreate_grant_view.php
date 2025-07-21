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
        DB::statement(
            "CREATE OR REPLACE VIEW event_grant_view AS
            select `eg`.`id`                                                            AS `id`,
       `eg`.`deleted_at`                                                    AS `deleted_at`,
       `eg`.`event_id`                                                      AS `event_id`,
       json_unquote(json_extract(`eg`.`title`, '$.fr'))                     AS `title`,
       coalesce(concat(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A')  AS `contact`,
       json_unquote(json_extract(`eg`.`comment`, '$.fr'))                   AS `comment`,
       ucase(`eg`.`amount_type`)                                            AS `amount_type`,
       `eg`.`amount`                                                        AS `amount`,
       format(`eg`.`amount` / 100.0, 2)                                     AS `amount_display`,
       `eg`.`pec_fee`                                                       AS `pec_fee`,
       format(`eg`.`pec_fee` / 100.0, 2)                                    AS `pec_fee_display`,
       `eg`.`pax_avg`                                                       AS `pax_avg`,
       `eg`.`pax_max`                                                       AS `pax_max`,
       if(`eg`.`active` = 1, 'Oui', 'Non')                                  AS `active`,
       format(coalesce(sum(`fr`.`total_net` + `fr`.`total_vat`), 0) / 100.0, 2)                   AS `amount_used`,
       format((`eg`.`amount` - coalesce(sum(`fr`.`total_net` + `fr`.`total_vat`), 0)) / 100.0, 2) AS `amount_remaining`,
       coalesce(count(distinct `fr`.`order_id`), 0)                         AS `order_count`,
       coalesce(count(distinct `o`.`client_id`), 0)                         AS `pax_count`
from ((((`event_grant` `eg` left join `event_grant_contact` `egc`
         on (`eg`.`id` = `egc`.`grant_id`)) left join `pec_distribution` `fr`
        on (`eg`.`id` = `fr`.`grant_id`)) left join `orders` `o` on (`fr`.`order_id` = `o`.`id`)) left join `events` `e`
      on (`eg`.`event_id` = `e`.`id`))
where `eg`.`deleted_at` is null
    and `e`.`deleted_at` is null
group by `eg`.`id`, `eg`.`deleted_at`, `eg`.`event_id`, json_unquote(json_extract(`eg`.`title`, '$.fr')),
         coalesce(concat(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A'),
         json_unquote(json_extract(`eg`.`comment`, '$.fr')), `eg`.`amount_type`, `eg`.`amount`,
         format(`eg`.`amount` / 100.0, 2), `eg`.`pec_fee`, format(`eg`.`pec_fee` / 100.0, 2), `eg`.`pax_avg`,
         `eg`.`pax_max`, `eg`.`active`"

        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
