<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_view AS
        select `e`.`id` AS `id`, e.deleted_at, `e`.`bank_card_code` AS `codecb`,date_format(`e`.`starts`,'%d/%m/%Y') AS `starts`,date_format(`e`.`ends`,'%d/%m/%Y') AS `ends`,json_unquote(json_extract(`et`.`name`,'$.fr')) AS `name`,json_unquote(json_extract(`et`.`subname`,'$.fr')) AS `subname`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `parent`,json_unquote(json_extract(`d2`.`name`,'$.fr')) AS `type`,concat_ws(' ',`u`.`first_name`,`u`.`last_name`) AS `admin`,case when `e`.`published` = 1 then 'Oui' when `e`.`published` is null then 'Non' else 'other' end AS `published` from ((((`events` `e` left join `events_texts` `et` on(`e`.`id` = `et`.`event_id`)) left join `users` `u` on(`e`.`admin_id` = `u`.`id`)) left join `dictionnary_entries` `d` on(`e`.`event_main_id` = `d`.`id`)) left join `dictionnary_entries` `d2` on(`e`.`event_type_id` = `d2`.`id`))
");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_view");
    }
};
