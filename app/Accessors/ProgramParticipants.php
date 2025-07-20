<?php

namespace App\Accessors;

use App\Enum\DesiredTransportManagement;
use App\Enum\EventProgramParticipantStatus;
use App\Models\DictionnaryEntry;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\EventManager\Program\EventProgramIntervention;
use App\Models\EventManager\Program\EventProgramSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProgramParticipants
{
    public static function getModeratorOratorInfo(string $type, EventContact $participant, array $participantInfo = [], ?int $interventionOrSessionId = null): array
    {
        $isOrator = 'orator' === $type;
        $defaultInterventionStatus = EventProgramParticipantStatus::default();
        $oratorName = $participant->account ? ucfirst(strtolower($participant->account->last_name)) . " " . ucfirst(strtolower($participant->account->first_name)) : '';
        $oratorSortName = strtolower($oratorName);
        $transport = $participant->transport;
        $interventionStatus = $defaultInterventionStatus;
        $allowPdf = false;
        $allowVideo = false;
        $moderatorTypeId = null;
        $eventContactInterventionId = null;

        if ($participantInfo) {
            if ($isOrator) {
                $interventionStatus = $participantInfo['status'] ?? $defaultInterventionStatus;
                $allowPdf = $participantInfo['allow_pdf_distribution'] ?? 0;
                $allowVideo = $participantInfo['allow_video_distribution'] ?? 0;
            } else {
                $interventionStatus = $participantInfo['status'] ?? $defaultInterventionStatus;
                $allowVideo = $participantInfo['allow_video_distribution'] ?? 0;
                $moderatorTypeId = $participantInfo['moderator_type_id'] ?? array_keys(Dictionnaries::selectValues('program_moderator_type'))[0];
            }
        } elseif ($interventionOrSessionId) {

            if ($isOrator) {
                $intervention = $participant->programInterventionOrators->firstWhere('event_program_intervention_id', $interventionOrSessionId);
            } else {
                $intervention = $participant->programSessionModerators->firstWhere('event_program_session_id', $interventionOrSessionId);
            }

            if ($intervention) {
                $eventContactInterventionId = $intervention->id;
                $interventionStatus = $intervention->status ? $intervention->status : $defaultInterventionStatus;
                if ($isOrator) {
                    $allowPdf = $intervention->allow_pdf_distribution;
                } else {
                    $moderatorTypeId = $intervention->moderator_type_id;
                }
                $allowVideo = $intervention->allow_video_distribution;
            }
        }

        if (!$isOrator && null === $moderatorTypeId) {
            $moderatorTypeId = array_keys(Dictionnaries::selectValues('program_moderator_type'))[0];
        }


        $desiredTransportManagement = $transport?->desired_management ?
            DesiredTransportManagement::translated($transport->desired_management) : 'N/A';


        $showDepartureText = false;
        $showReturnText = false;
        $departureText = '';
        $returnText = '';
        $departureTime = '';
        $returnTime = '';

        if ($transport) {

            if ($transport->departure_end_time) {
                $departureTime = $transport->departure_end_time->format('H\hi');
            }
            if ($transport->return_start_time) {
                $returnTime = $transport->return_start_time->format('H\hi');
            }

            if ($transport->departureStep) {
                $showDepartureText = false !== stripos($transport->departureStep->name, 'ok');
            }
            if ($transport->returnStep) {
                $showReturnText = false !== stripos($transport->returnStep->name, 'ok');
            }
            if ($transport->departureTransportType) {
                $departureText = "Arrivée à $transport->departure_end_location en {$transport->departureTransportType->name} à $departureTime";
            }
            if ($transport->returnTransportType) {
                $returnText = "Départ de $transport->return_start_location en {$transport->returnTransportType->name} à $returnTime";
            }

        }

        return [
            'id' => $participant->id,
            'event_contact_intervention_id' => $eventContactInterventionId,
            'name' => $oratorName,
            'lower_name' => $oratorSortName,
            'intervention_status' => $interventionStatus,
            'allow_pdf_distribution' => $allowPdf,
            'allow_video_distribution' => $allowVideo,
            'moderator_type_id' => $moderatorTypeId,

            //
            'desired_transport_management' => $desiredTransportManagement,
            'departure_text' => $departureText,
            'show_departure_text' => $showDepartureText,
            'return_text' => $returnText,
            'show_return_text' => $showReturnText,
        ];
    }


    public static function getParticipantInfo(int $eventId, ?int $interventionId = null, ?string $searchQuery = null, array $excludedParticipantIds = [], string $participantType = 'orator'): Collection
    {


        $participants = EventContact::query()
            ->where('event_id', $eventId)
            ->whereNotIn('id', $excludedParticipantIds)
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->whereHas('user', function ($query) use ($searchQuery) {
                    $query->where('first_name', 'like', '%' . $searchQuery . '%')
                        ->orWhere('last_name', 'like', '%' . $searchQuery . '%');
                });
            })
            ->with(['user', 'programInterventionOrators', 'transport'])
            ->get()
            ->map(function ($participant) use ($interventionId, $excludedParticipantIds, $participantType) {
                if ('orator' === $participantType) {
                    return ProgramInterventionOrators::getOratorInfo($participant, [], $interventionId);
                } else {
                    return ProgramSessionModerators::getModeratorInfo($participant, [], $interventionId);
                }
            });

        return $participants;
    }

}
