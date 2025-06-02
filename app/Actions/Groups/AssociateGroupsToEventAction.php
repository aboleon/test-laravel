<?php

namespace App\Actions\Groups;

use App\Accessors\GroupAccessor;
use App\Actions\EventManager\EventAssociator;
use MetaFramework\Traits\Ajax;
use Throwable;

class AssociateGroupsToEventAction
{
    use Ajax;
    public function associateGroupsToEvent(): array
    {
        try{
            $eventId = request('associateGroupsToEvent.event_id');
            if(null === $eventId){
                $eventId = request('event_id');
            }

            $ids = explode(',', request('ids'));

            return (new EventAssociator(
                type: "group",
                event_id: $eventId,
                ids: $ids
            ))->associate();
        } catch (Throwable $e) {
            $this->responseException($e);
        }
        return $this->fetchResponse();
    }
}
