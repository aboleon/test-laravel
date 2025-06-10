<?php

namespace App\Http\Controllers;

use App\Accessors\EventManager\EventDepositStats;
use App\Dashboards\Queries\DashboardParticipantsQuery;
use App\Dashboards\Queries\EventContactCountByServiceFamilyQuery;
use App\Dashboards\Queries\EventContactsWhitoutAnyOrderQuery;
use App\Dashboards\Queries\PecAndGrantDepositOrdersQuery;
use App\Dashboards\Queries\UnpaidDepositsQuery;
use App\Dashboards\Traits\DashboardTrait;
use App\Models\Event;
use App\Models\EventManager\Sellable;
use Illuminate\Contracts\Support\Renderable;

class EventManagerController extends Controller
{
    use DashboardTrait;
    /**
     * Display the specified resource.
     */
    public function show(Event $event): Renderable
    {
        $deposits  = new EventDepositStats($event);
        $deposits_sellable  = new EventDepositStats($event, Sellable::class);
        return view('events.manager.dashboard')
            ->with([
                'instance' => $this,
                'event' => $event->loadCount(['accommodation','sellableService', 'grantDeposit']),
                'deposits' => $deposits,
                'deposits_sellable' => $deposits_sellable,
                'statusOrder' => $deposits->getStatusOrder(),
                'participantsStats' => new DashboardParticipantsQuery()->setEvent($event)->run(),
                'unpaidDepositsStats' => new UnpaidDepositsQuery()->setEvent($event)->run(),
                'pecAndGrantDepositStats' => new PecAndGrantDepositOrdersQuery()->setEvent($event)->run(),
                'eventContactsWhitoutAnyOrder' => new EventContactsWhitoutAnyOrderQuery()->setEvent($event)->run(),
                'eventContactCountByServiceFamily' => new EventContactCountByServiceFamilyQuery()->setEvent($event)->run(),
            ]);
    }


}
