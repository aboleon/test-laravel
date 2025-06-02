<?php

namespace App\Accessors;

use App\Abstract\Orders;
use App\Accessors\EventManager\EventGroups;
use App\DataTables\View\EventGroupContactView;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Enum\OrderOrigin;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\Group;
use App\Models\GroupAddress;
use App\Services\Validators\AddressValidator;
use App\Traits\ModelSetters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use MetaFramework\Traits\Ajax;
use MetaFramework\Traits\Responses;

class GroupAccessor extends Orders
{
    use Ajax;
    use ModelSetters;
    use Responses;


    public function __construct(public null|int|Group $group = null)
    {
        if (is_int($this->group)) {
            $this->group = Group::find($this->group);
        }
    }

    public static function filter(?string $expression = null): Builder
    {
        $key   = 'id';
        $value = 'text';

        return Group::query()
            ->select('id as '.$key, 'name as '.$value)
            ->orderBy('name')
            ->when($expression, function ($query) use ($expression) {
                return $query->where(function ($q) use ($expression) {
                    $q->where('name', 'like', '%'.$expression.'%');
                });
            });
    }


    public function billingAddress(): ?GroupAddress
    {
        if ($this->group->address->isEmpty()) {
            return null;
        }
        $billingAddress = $this->group->address->filter(fn($item) => ! is_null($item->billing))->first();

        return $billingAddress ?? $this->group->address->first();
    }

    public function hasValidBillingAddress(): bool
    {
        return (new AddressValidator($this->billingAddress()))->isValid();
    }

    public static function allIds(): array
    {
        return Group::all()->pluck('id')->toArray();
    }

    public function contacts(array $options = []): ?Collection
    {
        if ($this->group->contacts->isEmpty()) {
            return null;
        }

        $eventGroup = $options['eventGroup'] ?? null;


        $ids    = $this->group->contacts->pluck('user_id')->toArray();
        $sortBy = $options['sortBy'] ?? null;

        return Accounts::getContactsFromPool($ids, [
            "main_contact_id" => $eventGroup?->mainContact?->id,
            "sortBy"          => $sortBy,
        ]);
    }

    public function getMainContactForEvent(): ?EventContact
    {
        if ( ! $this->event) {
            return null;
        }

        $eventGroupAccessor = (new EventGroups())->setEvent($this->event)->setGroup($this->group);

        return $eventGroupAccessor->getMainContactForEvent();
    }

    public static function getGroupsSelectableByEventId(int $eventId): array
    {
        $event = Event::with('groups')->find($eventId);
        if ($event && $event->groups->isNotEmpty()) {
            return $event->groups->pluck('name', 'id')->sort()->toArray();
        }

        return [];
    }

    public function getParticipantsForEvent(int $event_id): Collection
    {
        return EventGroupContactView::query()->where(['event_group_contact_view.event_id' => $event_id, 'group_id' => $this->group->id])
            ->join('events_contacts as a', function ($join) {
                $join
                    ->on('a.event_id', '=', 'event_group_contact_view.event_id')
                    ->on('a.user_id', '=', 'event_group_contact_view.user_id');
            })
            ->select('a.id', 'event_group_contact_view.user_id', DB::raw('CONCAT_WS(" ", first_name, UPPER(last_name)) as name'))
            ->orderBy('last_name')
            ->get();
    }


    /**
     * Retourne le stock groupé de chambres par date
     */
    public function summarizedAccommodationQuery(): array
    {
        return DB::select(
            "SELECT
            a.date,
            a.event_hotel_id,
            a.room_group_id,
            a.room_id,
            b.name as room_category,
            c.name as room,
            h.name as hotel_name,
            h.stars,
            ta.text_address AS hotel_address,
            d.capacity,
            SUM(a.quantity) AS original_quantity,
            SUM(
                CASE
                    WHEN JSON_EXTRACT(o.configs, '$.cant_attribute') IS NOT NULL
                    THEN a.quantity
                    ELSE 0
                END
            ) AS untouchable_quantity,
            SUM(a.quantity) - SUM(
                CASE
                    WHEN JSON_EXTRACT(o.configs, '$.cant_attribute') IS NOT NULL
                    THEN a.quantity
                    ELSE 0
                END
            ) AS total_quantity,
        o.configs
        FROM orders o
        JOIN order_cart_accommodation a ON a.order_id = o.id
        JOIN event_accommodation_room_groups b ON a.room_group_id = b.id
        JOIN event_accommodation e ON a.event_hotel_id = e.id
        JOIN hotels h ON e.hotel_id = h.id
        JOIN event_accommodation_room d ON a.room_id = d.id
        JOIN dictionnary_entries c ON d.room_id = c.id
        LEFT JOIN hotel_address AS ta ON h.id = ta.id
        WHERE o.client_type = '".OrderClientType::GROUP->value."'
          AND o.event_id = '".$this->event->id."'
          AND o.client_id = ".$this->group->id."
          AND o.origin = '".OrderOrigin::BACK->value."'
        GROUP BY
            a.date,
            a.event_hotel_id,
            a.room_group_id,
            a.room_id,
            h.name
        ORDER BY h.name;",
        );
    }

    /**
     * Retourne le stock de chambres achetées par ordre, date, et type de chambre
     */
    public function stockAccommodationQuery(): array
    {
        return DB::select(
            "SELECT
                        a.order_id,
                        a.date,
                        a.room_id,
                        a.quantity
                    FROM orders o
                    JOIN order_cart_accommodation a ON a.order_id = o.id
                    JOIN event_accommodation_room d ON a.room_id = d.id
                    WHERE o.client_type = '".OrderClientType::GROUP->value."'
                          AND o.event_id = '".$this->event->id."'
                          AND o.client_id = ".$this->group->id."
                          AND o.origin = '".OrderOrigin::BACK->value."'
                    ORDER BY a.order_id",
        );
    }

    /**
     * Retourne le stock de prestations achetées par ordre
     */
    public function stockServiceQuery(): array
    {
        return DB::select(
            "SELECT
                        a.order_id,
                        a.service_id,
                        a.quantity
                    FROM orders o
                    JOIN order_cart_service a ON a.order_id = o.id
                    WHERE o.client_type = '".OrderClientType::GROUP->value."'
                          AND o.event_id = '".$this->event->id."'
                          AND o.client_id = ".$this->group->id."
                          AND o.origin = '".OrderOrigin::BACK->value."'
                    ORDER BY a.order_id",
        );
    }

    public function summarizedServiceQuery(): array
    {
        return DB::select(

            "SELECT
            a.service_id,
            b.title AS service_name,
            SUM(a.quantity) AS originally_ordered,
            SUM(
                CASE
                    WHEN JSON_EXTRACT(o.configs, '$.cant_attribute') IS NOT NULL
                    THEN a.quantity
                    ELSE 0
                END
            ) AS untouchable_quantity,
            SUM(a.quantity) - SUM(
                CASE
                    WHEN JSON_EXTRACT(o.configs, '$.cant_attribute') IS NOT NULL
                    THEN a.quantity
                    ELSE 0
                END
            ) AS ordered,
            SUM(IFNULL(c.attributed_quantity, 0)) AS attributed,
        o.configs
        FROM orders o
        JOIN order_cart_service a ON a.order_id = o.id
        JOIN event_sellable_service b ON a.service_id = b.id
        LEFT JOIN (
            SELECT
                shoppable_id,
                order_id,
                SUM(quantity) AS attributed_quantity
            FROM order_attributions
            WHERE shoppable_type = '".OrderCartType::SERVICE->value."'
            GROUP BY shoppable_id, order_id
        ) c ON a.order_id = c.order_id AND a.service_id = c.shoppable_id
        WHERE o.client_type = '".OrderClientType::GROUP->value."'
          AND o.event_id = '".$this->event->id."'
          AND o.client_id = ".$this->group->id."
          AND o.origin = '".OrderOrigin::BACK->value."'
        GROUP BY a.service_id;",
        );
    }

    public function hasGroupOrders(): bool
    {
        $result = DB::selectOne(
            "SELECT
            COUNT(*) AS count
         FROM orders o
         WHERE o.client_type = ?
           AND o.event_id = ?
           AND o.client_id = ?
           AND o.origin = ?",
            [
                OrderClientType::GROUP->value,
                $this->event->id,
                $this->group->id,
                OrderOrigin::BACK->value,
            ],
        );

        return $result->count > 0;
    }
}
