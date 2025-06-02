<?php

namespace App\DataTables;

use App\Models\Event;

class EventContactDashboardTransportDataTable extends EventTransportDataTable
{
    public function __construct(Event $event)
    {
        parent::__construct($event);
        $this->setFilterByDesiredManagement(false);
        $this->htmlParams = [];
        $this->htmlId = "eventmanager-contact-dashboard-transport";
        $this->target = "eventContactDashboard";
    }


}
