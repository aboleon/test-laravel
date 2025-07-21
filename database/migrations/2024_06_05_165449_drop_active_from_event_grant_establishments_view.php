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
        DB::statement("CREATE OR REPLACE VIEW event_grant_establishments_view AS
            select `eg`.`grant_id`                                AS `grant_id`,
       `eg`.`pax`                                     AS `pax`,
       `e`.`id`                                       AS `id`,
       `e`.`name`                                     AS `name`,
       `e`.`country_code`                             AS `country_code`,
       `e`.`type`                                     AS `type`,
       `e`.`street_number`                            AS `street_number`,
       `e`.`route`                                    AS `route`,
       `e`.`postal_code`                              AS `postal_code`,
       `e`.`locality`                                 AS `locality`,
       `e`.`administrative_area_level_1`              AS `administrative_area_level_1`,
       `e`.`administrative_area_level_2`              AS `administrative_area_level_2`,
       `e`.`text_address`                             AS `text_address`,
       `e`.`lat`                                      AS `lat`,
       `e`.`lon`                                      AS `lon`,
       `e`.`created_at`                               AS `created_at`,
       `e`.`updated_at`                               AS `updated_at`,
       `e`.`deleted_at`                               AS `deleted_at`,
       `e`.`prefix`                                   AS `prefix`,
       json_unquote(json_extract(`c`.`name`, '$.fr')) AS `country`
from ((`event_grant_establishments` `eg` join `establishments` `e`
       on (`e`.`id` = `eg`.`establishment_id`)) join `countries` `c` on (`c`.`code` = `e`.`country_code`))"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // recreate impossible; active field is no longer present
    }
};
