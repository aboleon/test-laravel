<?php

namespace App\Http\Controllers;

use App\Accessors\EventManager\EventDepositStats;
use App\Models\Event;
use App\Models\EventManager\Sellable;

class EventManagerController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $deposits  = new EventDepositStats($event);
        $deposits_sellable  = new EventDepositStats($event, Sellable::class);
        return view('events.manager.dashboard')
            ->with([
                'event' => $event->loadCount(['accommodation','sellableService', 'grantDeposit']),
                'deposits' => $deposits,
                'deposits_sellable' => $deposits_sellable,
                'statusOrder' => $deposits->getStatusOrder(),
            ]);
    }

}
