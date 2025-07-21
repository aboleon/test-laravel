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
        DB::statement("CREATE OR REPLACE VIEW event_program_intervention_view AS

select `i`.`id`                                                                                                 AS `id`,
       `i`.`is_catering`                                                                                        AS `is_catering`,
       `i`.`is_placeholder`                                                                                     AS `is_placeholder`,
       `dr`.`event_id`                                                                                          AS `event_id`,
       `i`.`event_program_session_id`                                                                           AS `event_program_session_id`,
       concat(date_format(`dr`.`datetime_start`, '%d/%m/%Y'), ' - ', `pmain`.`name`, ' > ',
              json_unquote(json_extract(`rmain`.`name`, '$.fr')))                                               AS `container`,
       concat(date_format(min(`i`.`start`), '%Hh%i'), ' - ',
              date_format(max(`i`.`end`), '%Hh%i'))                                                             AS `timings`,
       json_unquote(json_extract(`s`.`name`, '$.fr'))                                                           AS `session`,
       json_unquote(json_extract(`i`.`name`, '$.fr'))                                                           AS `name`,
       group_concat(concat(`u`.`last_name`, ' ', `u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `orators`,
       json_unquote(json_extract(`dictionnary_entries`.`name`, '$.fr'))                                         AS `specificity`,
       `i`.`duration`                                                                                           AS `duration`
from ((((((((`event_program_interventions` `i` join `event_program_sessions` `s`
             on (`i`.`event_program_session_id` = `s`.`id`)) join `event_program_day_rooms` `dr`
            on (`s`.`event_program_day_room_id` = `dr`.`id`)) join `place_rooms` `rmain`
           on (`dr`.`room_id` = `rmain`.`id`)) join `places` `pmain`
          on (`rmain`.`place_id` = `pmain`.`id`)) left join `dictionnary_entries`
         on (`i`.`specificity_id` = `dictionnary_entries`.`id`)) left join `event_program_intervention_orators` `eci`
        on (`eci`.`event_program_intervention_id` = `i`.`id`)) left join `events_contacts` `c`
       on (`c`.`id` = `eci`.`events_contacts_id`)) left join `users` `u` on (`u`.`id` = `c`.`user_id`))
group by `i`.`id`, `dr`.`datetime_start`, `i`.`start`, `i`.`end`, `s`.`name`, `i`.`name`, `dictionnary_entries`.`name`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_program_intervention_view AS
select `i`.`id` AS `id`,`i`.`is_catering` AS `is_catering`,`i`.`is_placeholder` AS `is_placeholder`,`dr`.`event_id` AS `event_id`,`i`.`event_program_session_id` AS `event_program_session_id`,concat(date_format(`dr`.`datetime_start`,'%d/%m/%Y'),' - ',`pmain`.`name`,' > ',json_unquote(json_extract(`rmain`.`name`,'$.fr'))) AS `container`,concat(date_format(min(`i`.`start`),'%Hh%i'),' - ',date_format(max(`i`.`end`),'%Hh%i')) AS `timings`,json_unquote(json_extract(`s`.`name`,'$.fr')) AS `session`,json_unquote(json_extract(`i`.`name`,'$.fr')) AS `name`,group_concat(concat(`u`.`last_name`,' ',`u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `orators`,json_unquote(json_extract(`dictionnary_entries`.`name`,'$.fr')) AS `specificity`,`i`.`duration` AS `duration`,case when `i`.`is_online` = 1 then 'Oui' else 'Non' end AS `is_online` from ((((((((`event_program_interventions` `i` join `event_program_sessions` `s` on(`i`.`event_program_session_id` = `s`.`id`)) join `event_program_day_rooms` `dr` on(`s`.`event_program_day_room_id` = `dr`.`id`)) join `place_rooms` `rmain` on(`dr`.`room_id` = `rmain`.`id`)) join `places` `pmain` on(`rmain`.`place_id` = `pmain`.`id`)) left join `dictionnary_entries` on(`i`.`specificity_id` = `dictionnary_entries`.`id`)) left join `event_program_intervention_orators` `eci` on(`eci`.`event_program_intervention_id` = `i`.`id`)) left join `events_contacts` `c` on(`c`.`id` = `eci`.`events_contacts_id`)) left join `users` `u` on(`u`.`id` = `c`.`user_id`)) group by `i`.`id`,`dr`.`datetime_start`,`i`.`start`,`i`.`end`,`s`.`name`,`i`.`name`,`dictionnary_entries`.`name` ");
    }
};
