<?php

namespace App\Traits;

use App\Accessors\EventManager\Accommodations;
use App\Accessors\OrderAccessor;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Models\Event;
use App\Models\Order\Attribution;
use App\Models\Order\Cart\AccommodationCart;
use App\Traits\Front\Groups;
use Illuminate\Database\Eloquent\Collection;

trait AttributionTrait
{
    use Groups;

    private readonly Collection $groupMembers;
    protected readonly Event $event;
    protected readonly OrderAccessor $orderAccessor;
    protected readonly string $locale;
    protected readonly string $type;
    protected $ordered;

    protected bool $forOrder = false;

    protected function setGroupMembers(): void
    {
        $this->groupMembers = $this->groupAccessor->getParticipantsForEvent($this->event->id);
    }

    protected function baseViewData(): array
    {
        return [
            'locale'        => $this->locale,
            'type'          => $this->type,
            'event'         => $this->event,
            'groupAccessor' => $this->groupAccessor,
            'groupMembers'  => $this->groupMembers,
        ];
    }

    protected function getBookingsForMembers(): Collection
    {
        // $accomodation_ids = $this->order->accommodation->pluck('event_hotel_id')->unique();

        return AccommodationCart::query()
            ->selectRaw(
                "
        order_id,
        date,
        o.client_id,
        event_hotel_id,
        JSON_UNQUOTE(JSON_EXTRACT(rg.name, '$.fr')) as room_category,
        order_cart_accommodation.room_group_id,
        order_cart_accommodation.room_id,
        JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) as room_label,
        r.room_id,
        h.name as hotel_name,
        r.capacity as room_capacity
    ",
            )
            ->join(
                'orders as o',
                function ($join) {
                    $join->on('o.id', '=', 'order_id')
                        ->where(fn($where) =>
                        $where->where(['event_id' => $this->event->id])
                            ->where('client_type', '!=', OrderClientType::GROUP->value)
                        )
                        ->whereIn('client_id', $this->groupMembers->pluck('user_id'));

                    if ($this->forOrder) {
                        $join->where('o.id', '!=', $this->order->id);
                    }
                }
            )
            ->join('event_accommodation_room_groups as rg', fn($join) => $join->on('rg.id', '=', 'room_group_id'))
            ->join('event_accommodation_room as r', fn($join) => $join->on('r.id', '=', 'order_cart_accommodation.room_id'))
            ->join('dictionnary_entries as d', fn($join) => $join->on('d.id', '=', 'r.room_id'))
            ->join('event_accommodation as ea', fn($join) => $join->on('ea.id', '=', 'event_hotel_id'))
            ->join('hotels as h', fn($join) => $join->on('h.id', '=', 'ea.hotel_id'))
            ->get();
    }

    protected function getAttributionsForMembers(): Collection
    {
        $data = Attribution::query()
            ->whereIn('event_contact_id', $this->groupMembers->pluck('id'));
        if ($this->forOrder) {
            $data->where('order_id', '!=', $this->order->id);
        }

        return $data->get();
    }

    protected function getAttributionsHotels(array $order_ids = []): Collection
    {
        return Attribution::query()
            ->selectRaw(
                "
        order_attributions.id as attribution_id,
        order_attributions.order_id,
        DATE_FORMAT(JSON_UNQUOTE(JSON_EXTRACT(order_attributions.configs, '$.date')), '%d/%m/%Y') as date_formatted,
        JSON_UNQUOTE(JSON_EXTRACT(order_attributions.configs, '$.date')) as date,
        order_attributions.event_contact_id,
        ac.event_hotel_id,
        JSON_UNQUOTE(JSON_EXTRACT(rg.name, '$.fr')) as room_category,
        ac.room_group_id,
        ac.room_id,
        JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) as room_label,
        r.room_id,
        h.name as hotel_name,
        r.capacity as room_capacity,
        order_attributions.quantity as attribution_quantity
    ",
            )
            ->where('order_attributions.shoppable_type','=', OrderCartType::ACCOMMODATION->value)
            ->whereIn('order_attributions.order_id', $order_ids)
            ->join(
                'orders as o',
                fn($join)
                    => $join
                    ->on('o.id', '=', 'order_attributions.order_id'),
            )
            ->join('order_cart_accommodation as ac', fn($join) => $join->on('o.id', '=', 'ac.order_id'))
            ->join('event_accommodation_room_groups as rg', fn($join) => $join->on('rg.id', '=', 'ac.room_group_id'))
            ->join('event_accommodation_room as r', fn($join) => $join->on('r.id', '=', 'ac.room_id'))
            ->join('dictionnary_entries as d', fn($join) => $join->on('d.id', '=', 'r.room_id'))
            ->join('event_accommodation as ea', fn($join) => $join->on('ea.id', '=', 'ac.event_hotel_id'))
            ->join('hotels as h', fn($join) => $join->on('h.id', '=', 'ea.hotel_id'))
            ->get();
    }

    protected function getRooms(): \Illuminate\Support\Collection
    {
        return $this->ordered->flatten()->map(fn($item)
            => [
            'capacity'      => $item->capacity,
            'room_group_id' => $item->room_group_id,
            'room_id'       => $item->room_id,
            'room_category' => json_decode($item->room_category)->{$this->locale},
            'room'          => json_decode($item->room)->{$this->locale},
        ]);
    }

    protected function getCrossAccommodationAttributions(): Collection
    {
        return $this->getAttributionsForMembers()->filter(fn($item) => $item->shoppable_type == OrderCartType::ACCOMMODATION->value,
        );
    }

    protected function getAccommodationViewData():array
    {
        $crossAttributions = $this->getCrossAccommodationAttributions();
;
        return [
            'bookedForMembers'          => $this->getBookingsForMembers(),
            'rawAttributionsForMembers' => $crossAttributions,
            'attributionsForMembers'    => $this->getAttributionsHotels($crossAttributions->pluck('order_id')->toArray()),
            'hotels'                    => Accommodations::hotelLabelsWithStatus($this->event),
            'ordered'                   => $this->ordered,
            'rooms'                     => $this->getRooms(),
        ];
    }

}
