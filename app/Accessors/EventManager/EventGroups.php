<?php

namespace App\Accessors\EventManager;

use App\DataTables\View\EventGroupContactView;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use App\Models\Group;
use App\Models\Order;
use App\Models\Order\Invoiceable;
use App\Models\User;
use App\Traits\ModelSetters;
use Illuminate\Database\Eloquent\Collection;

class EventGroups
{
    use ModelSetters;

    protected ?EventGroup $eventGroup = null;
    protected ?Group $group = null;

    private ?Collection $orders = null;

    private ?Collection $eventContacts = null;

    public function setEventGroup(EventGroup $eventGroup): self
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getEventGroupsSelectableByEvent(): array
    {
        if (!$this->event) {
            return [];
        }

        if ($this->event->eventGroups->isNotEmpty()) {
            return $this->event->eventGroups()->with('group')->get()->mapWithKeys(function ($eventGroup) {
                return [$eventGroup->id => $eventGroup->group->name];
            })->sort()->toArray();
        }

        return [];
    }


    public function getMainContactForEvent(): ?EventContact
    {
        if (!$this->event) {
            return null;
        }

        return EventContact::where([
            'user_id' =>
                EventGroup::where([
                    'group_id' => $this->group->id,
                    'event_id' => $this->event->id,
                ])
                    ->whereNotNull('main_contact_id')
                    ->value('main_contact_id'),
            'event_id' => $this->event->id,
        ],
        )->first();
    }


    public static function getGroupMembers(EventGroup $eventGroup): Collection
    {
        return EventGroupContactView::where('event_group_id', $eventGroup->id)
            ->orderBy('last_name', 'asc')
            ->get();
    }

    /**
     * @param EventContact|null $eventContact
     *
     * @return int|null
     * Returns the group id
     */
    public static function isAMainContact(?EventContact $eventContact = null): ?int
    {
        return $eventContact?->event->eventGroups()->where('main_contact_id', $eventContact->user_id)->first()?->group_id;
    }

    public static function getGroupByMainContact(Event $event, User $user): ?EventGroup
    {
        return EventGroup::where(['event_id' => $event->id, 'main_contact_id' => $user->id])->with('group')->first();
    }


    public function getEventContacts(): Collection
    {
        if ($this->eventContacts === null) {
            $this->eventContacts = $this->eventGroup->eventContacts;
        }

        return $this->eventContacts;
    }

    public function getEventGroupOrders(): Collection
    {
        if ($this->orders === null) {
            if($this->event == null){
                $this->setEvent($this->eventGroup->event);
            }

            $this->orders = Order::whereIn(
                'id',
                Order::query()
                    ->select('id as order_id')->where([
                        'event_id' => $this->event->id,
                        'client_type' => 'group',
                        'client_id' => $this->eventGroup->group_id,
                    ])
                    ->union(
                        Invoiceable::query()
                            ->select('order_id')
                            ->where([
                                'account_id' => $this->eventGroup->group_id,
                                'account_type' => 'group',
                            ])->join('orders as o', fn($join) => $join->on('o.id', '=', 'order_invoiceable.order_id')->where('o.event_id', $this->event->id)),
                    )->pluck('order_id'),
            )
                ->with(['group', 'account', 'invoiceable'])->get();
        }

        return $this->orders;
    }
}
