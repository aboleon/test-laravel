<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_program_sessions_view AS
        select `s`.`id`                                                                                                AS `id`,
       `s`.`is_catering`                                                                                       AS `is_catering`,
       `s`.`is_placeholder`                                                                                    AS `is_placeholder`,
       `dr`.`event_id`                                                                                         AS `event_id`,
       date_format(`dr`.`datetime_start`, '%d/%m/%Y')                                                          AS `date`,
       json_unquote(json_extract(`s`.`name`, '$.fr'))                                                          AS `name`,
       date_format(`dr`.`datetime_start`, '%d/%m/%Y')                                                          AS `datetime_start`,
       `p`.`name`                                                                                              AS `place_name`,
       json_unquote(json_extract(`pr`.`name`, '$.fr'))                                                         AS `place_room`,
       group_concat(distinct concat(`u`.`last_name`, ' ', `u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `moderators`,
       concat(date_format(min(`i`.`start`), '%Hh%i'), ' - ', date_format(max(`i`.`end`), '%Hh%i'))             AS `timings`,
       json_unquote(json_extract(`d`.`name`, '$.fr'))                                                          AS `sponsor`
from event_program_sessions `s`
left join event_program_day_rooms `dr` on (`s`.`event_program_day_room_id` = `dr`.`id`)
left join place_rooms `pr` on (`pr`.`id` = `s`.`place_room_id`)
left join places `p` on (`p`.`id` = `pr`.`place_id`)
left join event_program_session_moderators `m` on (`m`.`event_program_session_id` = `s`.`id`)
left join events_contacts `c` on (`c`.`id` = `m`.`events_contacts_id`)
left join users `u` on (`u`.`id` = `c`.`user_id`)
left join event_program_interventions `i` on (`i`.`event_program_session_id` = `s`.`id`)
left join dictionnary_entries `d` on (`d`.`id` = `s`.`sponsor_id`)
group by `s`.`id`, `dr`.`event_id`, `s`.`name`, `dr`.`datetime_start`, `pr`.`name`, `p`.`name`, `d`.`name`

");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_program_sessions_view AS select `s`.`id` AS `id`,`s`.`is_catering` AS `is_catering`,`s`.`is_placeholder` AS `is_placeholder`,`dr`.`event_id` AS `event_id`,date_format(`dr`.`datetime_start`,'%d/%m/%Y') AS `date`,json_unquote(json_extract(`s`.`name`,'$.fr')) AS `name`,date_format(`dr`.`datetime_start`,'%d/%m/%Y') AS `datetime_start`,concat(`p`.`name`,' > ',json_unquote(json_extract(`pr`.`name`,'$.fr'))) AS `place_room`,group_concat(distinct concat(`u`.`last_name`,' ',`u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `moderators`,concat(date_format(min(`i`.`start`),'%Hh%i'),' - ',date_format(max(`i`.`end`),'%Hh%i')) AS `timings` from (((((((`event_program_sessions` `s` join `event_program_day_rooms` `dr` on(`s`.`event_program_day_room_id` = `dr`.`id`)) left join `place_rooms` `pr` on(`pr`.`id` = `s`.`place_room_id`)) left join `places` `p` on(`p`.`id` = `pr`.`place_id`)) left join `event_program_session_moderators` `m` on(`m`.`event_program_session_id` = `s`.`id`)) left join `events_contacts` `c` on(`c`.`id` = `m`.`events_contacts_id`)) left join `users` `u` on(`u`.`id` = `c`.`user_id`)) left join `event_program_interventions` `i` on(`i`.`event_program_session_id` = `s`.`id`)) group by `s`.`id`,`dr`.`event_id`,`s`.`name`,`dr`.`datetime_start`,`pr`.`name`,`p`.`name` ");
    }
};
