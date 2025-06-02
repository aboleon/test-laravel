<?php

namespace App\DataTables;

use App\Models\Event;

class PecDataTable extends EventContactDataTable
{


    public function __construct(
        private readonly Event $event,
        private string         $group = 'all',
        private ?string        $withOrder = null
    )
    {
        parent::__construct($event, $group, $this->withOrder);
        $this->withLastGrantNotNull = true;
    }
}

