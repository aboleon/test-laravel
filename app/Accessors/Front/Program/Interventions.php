<?php

namespace App\Accessors\Front\Program;

use App\Accessors\ProgramSessions;
use App\Enum\EventProgramParticipantStatus;
use App\Helpers\DateHelper;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramInterventionOrator;
use App\Models\EventManager\Program\EventProgramSessionModerator;
use Carbon\Carbon;

class Interventions
{
    public static function getOratorModeratorItems(EventContact $eventContact)
    {

        $event = $eventContact->event;
        $orators = $eventContact->programInterventionOrators;
        $moderators = $eventContact->programSessionModerators;


        $moderatorItems = $moderators->map(function (EventProgramSessionModerator $moderator) use ($event) {

            $sm = ProgramSessions::getPracticalSummary($moderator->session);
            $summaryText = $sm['date'] . " " . $sm['start_time'] . ", durée " . $sm['duration'] . " - " . $sm['room'];


            $badge = self::getParticipantStatusBadge($moderator);

            return array_merge([
                'title' => $moderator->moderatorType->name,
                'text' => '<b>' . $moderator->session->name . '</b><br>' . $summaryText,
//                'actions' => [
//                    [
//                        'type' => 'link',
//                        'class' => 'btn-primary',
//                        'title' => 'Voir',
//                        'url' => route('front.event.intervention.edit', $event),
//                    ],
//                ],
            ], $badge);
        });

        $oratorItems = $orators->map(function (EventProgramInterventionOrator $orator) use ($event) {
            $carbon = Carbon::create($orator->intervention->start);
            $duration = $orator->intervention->intervention_timing_details ?? DateHelper::convertMinutesToReadableDuration($orator->intervention->duration);
            $room = $orator->intervention->session->room->name;
            $summaryText = $carbon->format(config("app.date_display_format")) . " " . $carbon->format("H\hi") . ", durée: $duration - " . $room;

            $badge = self::getParticipantStatusBadge($orator);

            return array_merge([
                'title' => $orator->intervention->name,
                'text' => '<b>' . $orator->intervention->session->name . '</b><br>' . $summaryText,
//                'actions' => [
//                    [
//                        'type' => 'link',
//                        'class' => 'btn-primary',
//                        'title' => 'Voir',
//                        'url' => route('front.event.intervention.edit', $event),
//                    ],
//                ],
            ], $badge);
        });


        return $moderatorItems->concat($oratorItems);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getParticipantStatusBadge(EventProgramInterventionOrator|EventProgramSessionModerator $p)
    {
        return match ($p->status) {
            EventProgramParticipantStatus::VALIDATED->value => self::getBadge('bg-success', 'Accepté'),
            EventProgramParticipantStatus::DENIED->value => self::getBadge('bg-danger', 'Refusé'),
            EventProgramParticipantStatus::PENDING->value => self::getBadge('bg-info', 'En attente'),
            default => self::getBadge('bg-secondary', 'Non renseigné'),
        };
    }

    private static function getBadge($bgClass, $text)
    {
        return [
            'badge' => [
                'class' => $bgClass . ' rounded-pill',
                'text' => $text,
            ],
        ];
    }
}