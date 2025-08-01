<?php

namespace App\Actions\EventManager\Program;


use App\Accessors\Dictionnaries;
use App\Models\EventManager\Program\EventProgramInterventionOrator;
use Illuminate\Database\Eloquent\Builder;

class OratorsAction
{

    public function getOratorsByIntervention(int $interventionId): array
    {
        $arr = $this->getBaseQuery()
            ->where('event_program_intervention_id', $interventionId)
            ->get()
            ->toArray();

        $this->decorateWithDictionaries($arr);

        return $arr;
    }

    public function getOratorsBySession(int $sessionId): array
    {
        $arr = $this->getBaseQuery()
            ->whereHas('intervention', function ($query) use ($sessionId) {
                $query->where('event_program_session_id', $sessionId);
            })
            ->get()
            ->toArray();

        $this->decorateWithDictionaries($arr);

        return $arr;
    }

    public function getOratorsByContainer(int $containerId): array
    {
        $arr = $this->getBaseQuery()
            ->whereHas('intervention.session', function ($query) use ($containerId) {
                $query->where('event_program_day_room_id', $containerId);
            })
            ->get()
            ->toArray();

        $this->decorateWithDictionaries($arr);

        return $arr;
    }


    public function getOratorsByEvent(int $eventId): array
    {
        $arr = $this->getBaseQuery()
            ->whereHas('intervention.session.programDay', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->get()
            ->toArray();

        $this->decorateWithDictionaries($arr);

        return $arr;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function decorateWithDictionaries(array &$arr): void
    {
        $map = [
            "base_id" => ["base", "base"],
            "domain_id" => ["domain", "domain"],
            "title_id" => ["titles", "title"],
            "profession_id" => ["professions", "profession"],
            "savant_society_id" => ["savant_societies", "savant_society"],
        ];

        $langMap = trans('mfw-lang');

        array_walk($arr, function (&$item) use ($map, $langMap) {
            $profile = &$item['event_contact']['account']['profile'];

            // Process dictionary mappings
            foreach ($map as $key => $info) {
                $profile[$info[1]] = isset($profile[$key])
                    ? Dictionnaries::entry($info[0], $profile[$key])
                    : null;
            }

            // Process language
            $profile['lang'] = isset($profile['lang'])
                ? ($langMap[$profile['lang']] ?? null)
                : null;
        });
    }

    private function getBaseQuery(): Builder
    {
        return EventProgramInterventionOrator::with([
            "eventContact.user",
            "eventContact.account.profile",
            "eventContact.account.address",
            "eventContact.account.phones",
            "eventContact.participationType",
        ]);
    }

}
