<?php

namespace App\Actions\Account;

use App\Enum\ParticipantType;
use App\Enum\SavedSearches;
use App\Events\EventContactCreated;
use App\Helpers\AdvancedSearch\ContactAdvancedSearchHelper;
use App\Helpers\CsvHelper;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\ParticipationType;
use App\Models\User;
use MetaFramework\Traits\Responses;
use Throwable;

class AssociateUsersToEventAction
{
    use Responses;

    public function associateUsersToEventByEventContact(): array
    {
        $eventContactIds = CsvHelper::csvToUniqueArray(request('ids', ''));
        $userIds = EventContact::whereIn('id', $eventContactIds)
            ->pluck('user_id')
            ->toArray();
        return $this->associateUsersToEventByUserIds($userIds);
    }

    public function associateUsersToEvent(): array
    {
        $userIds = CsvHelper::csvToUniqueArray(request('ids', ''));
        return $this->associateUsersToEventByUserIds($userIds);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function associateUsersToEventByUserIds(array $userIds): array
    {
        $this->enableAjaxMode();

        try {
            $eventId = request('associateUsersToEvent.event_id');
            $participationTypeId = request('associateUsersToEvent.participation_type_id');

            $this->responseElement('callback', 'redrawDataTable');

            $alreadyBoundUserIds = EventContact::where('event_id', $eventId)
                ->whereIn('user_id', $userIds)
                ->pluck('user_id')
                ->toArray();

            $newUserIds = array_diff($userIds, $alreadyBoundUserIds);

            if (!empty($alreadyBoundUserIds)) {
                $alreadyBoundUsers = User::whereIn('id', $alreadyBoundUserIds)->get();
                $alreadyBoundUserNames = $alreadyBoundUsers->map(function ($user) {
                    return $user->names();
                })->toArray();

                $event = Event::find($eventId);
                if(1 === count($alreadyBoundUserIds)){
                    $sStart = implode('', $alreadyBoundUserNames) . " est déjà participant";
                }
                else{
                    $sStart = "Les contacts " . implode(', ', $alreadyBoundUserNames) . " sont déjà participants";
                }

                $this->responseError($sStart . " pour l'événement \"{$event->texts->name}\".");
            }

            if($newUserIds) {
                $addedNames = [];
                foreach ($newUserIds as $userId) {
                    $eventContact = EventContact::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'event_id' => $eventId
                        ],
                        [
                            'participation_type_id' => $participationTypeId,
                            'created_at' => now(),
                        ]
                    );

                    event(new EventContactCreated($eventContact));
                    $addedNames[] = $eventContact->user->names();
                }


                $eventName = Event::find($eventId)->texts->name;

                $sParticipationType = "Participant";
                $participationType = ParticipationType::find($participationTypeId);
                if ($participationType) {
                    $sParticipationType = $participationType->name;
                }
                if(1 === count($addedNames)){
                    $sContact = implode('', $addedNames) . " a été affecté";
                }
                else{
                    $sContact = "Les contacts " . implode(', ', $addedNames) . " ont été affecté";
                }
                $this->responseSuccess($sContact . " à l'événement \"$eventName\" comme \"$sParticipationType\"");
            }

            return $this->fetchResponse();

        } catch (Throwable $e) {
            $this->responseException($e);
        }
        return $this->fetchResponse();
    }
}
