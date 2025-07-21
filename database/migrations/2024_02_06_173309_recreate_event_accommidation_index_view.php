<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW eventmanager_hotel_view AS
        select `a`.`id` AS `id`,`a`.`event_id` AS `event_id`,`a`.`hotel_id`,
(select COUNT(`bk`.`id`) FROM `order_cart_accommodation` `bk` WHERE `a`.`id` = `bk`.`event_hotel_id`) AS bookings,
JSON_UNQUOTE(json_extract(`a`.`title`,'$.fr')) AS `title`,`b`.`locality` AS `locality`,`d`.`name` AS `name`,`d`.`email` AS `email`,`d`.`phone` AS `phone`,
case when `a`.`pec` = 1 then 'Oui' when `a`.`pec` is null then 'Non' end AS `pec`,
case when `a`.`published` is null then 'Hors ligne' else 'En ligne' end AS `published`
from ((`event_accommodation` `a`
left join `hotels` `d` on(`a`.`hotel_id` = `d`.`id`))
left join `hotel_address` `b` on(`a`.`hotel_id` = `b`.`hotel_id`));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
