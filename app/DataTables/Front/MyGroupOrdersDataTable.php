<?php

namespace App\DataTables\Front;

use App\DataTables\View\FrontMyOrdersView;
use App\Enum\OrderClientType;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class MyGroupOrdersDataTable extends MyOrdersDataTable
{
    private EventGroup $eventGroup;

    public function __construct(EventContact $eventContact, EventGroup $eventGroup)
    {
        parent::__construct($eventContact);
        $this->eventGroup = $eventGroup;
        $this->orderDetailsRouteName = 'front.event.group.orders.edit';
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(FrontMyOrdersView $model): EloquentBuilder
    {
        return $model->newQuery()
            ->where('client_id', $this->eventGroup->group_id)
            ->where('client_type', OrderClientType::GROUP->value)
            ->where('event_id', $this->eventContact->event_id)
            ->where(function ($query) {
                $query->where('total_net', '!=', 0)
                    ->orWhere('total_pec', '!=', 0);
            });
    }
}
