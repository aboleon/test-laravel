<?php

namespace App\Http\Controllers\EventManager;

use App\Models\Event;

class DepositController
{
    public function index(Event $event)
    {
        return view('events.manager.grant.deposit.index', ['event' => $event->load('grantDeposit','sellableServicesWithDeposit.deposit')]);
    }
}
