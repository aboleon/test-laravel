<?php

namespace App\DataTables;

use App\Models\Event;

class EventTransportUndesiredManagementDataTable extends EventTransportDataTable
{
    public function __construct(Event $event)
    {
        parent::__construct($event, false);

    }


}
