<?php

namespace App\DataTables;

use App\DataTables\View\PecOrderView;
use App\Models\Event;
use App\Models\EventContact;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class EventContactPecDataTable extends PecOrderDataTable
{
    public function __construct(public Event $event, public EventContact $eventContact)
    {
        $grantCount = $event->grants()->count();

        parent::__construct($event, $grantCount);
    }

    public function query(PecOrderView $model): QueryBuilder
    {
        $query = parent::query($model);
        return $query
            ->where('event_contact_id', $this->eventContact->id);
    }

    public function html(): HtmlBuilder
    {
        return $this->setHtml('pec_order', [
            'minifiedAjaxUrl' => route('panel.manager.event.event_contact.pec.pec', ['event' => $this->event->id, 'eventContact' => $this->eventContact->id]),
        ])->autoWidth(false);
    }
}
