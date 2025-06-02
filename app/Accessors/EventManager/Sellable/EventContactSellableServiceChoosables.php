<?php

namespace App\Accessors\EventManager\Sellable;

use App\Enum\ApprovalResponseStatus;
use App\Models\Event;
use App\Models\EventContact;

use App\Models\EventManager\Sellable;
use App\Models\EventManager\Sellable\Choosable;
use App\Models\EventManager\Sellable\EventContactSellableServiceChoosable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EventContactSellableServiceChoosables
{
    private static $store = [];

    public static function getInfoByEventContactAndChoosable(EventContact $eventContact, Choosable $choosable): array
    {
        $item = EventContactSellableServiceChoosable::where('event_contact_id', $eventContact->id)
            ->where('choosable_id', $choosable->id)
            ->first();

        if ($item) {
            return [
                'status' => $item->status,
                'invitation_quantity_accepted' => $item->invitation_quantity_accepted,
            ];
        }
        return [];
    }

    public static function getInvitations(Sellable $sellable): Collection
    {
        return EventContactSellableServiceChoosable::where('choosable_id', $sellable->id)
            ->whereHas('choosable', function ($query) {
                $query->where('is_invitation', 1);
            })
            ->with(['choosable', 'eventContact.user'])
            ->get();
    }


    public static function getEventContactChosenChoosables(EventContact $ec): \Illuminate\Support\Collection
    {
        return $ec->choosables()->get()->filter(function ($c) {
            return $c->pivot->status !== ApprovalResponseStatus::PENDING->value;
        });
    }

    public static function getEventContactChoosables(Event $event, EventContact $eventContact): Collection
    {
        $key = $event->id . "-" . $eventContact->id;
        if (!array_key_exists($key, self::$store)) {


            $professionId = $eventContact->account->profile->profession_id;
            $participationId = $eventContact->participation_type_id;


            self::$store[$key] = $event->publishedChoosable()
                ->with(['place', 'room'])
                ->select(
                    'event_sellable_service.*',
                    'event_contact_sellable_service_choosables.*',
                    'event_sellable_service.id as id',
                )
                ->leftJoin("event_contact_sellable_service_choosables", "event_contact_sellable_service_choosables.choosable_id", "=", "event_sellable_service.id")
                ->whereExists(function ($query) use ($professionId) {
                    $query->select(DB::raw(1))
                        ->from('event_sellable_service_profession')
                        ->whereRaw("event_sellable_service_profession.event_sellable_service_id = event_sellable_service.id")
                        ->where('event_sellable_service_profession.profession_id', $professionId);
                })
                ->whereExists(function ($query) use ($participationId) {
                    $query->select(DB::raw(1))
                        ->from('event_sellable_service_participation')
                        ->whereRaw("event_sellable_service_participation.event_sellable_service_id = event_sellable_service.id")
                        ->where('event_sellable_service_participation.participation_id', $participationId);
                })
                ->groupBy('event_sellable_service.id')
                ->get();

        }
        return self::$store[$key];
    }
}
