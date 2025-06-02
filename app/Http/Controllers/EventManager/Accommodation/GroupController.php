<?php

namespace App\Http\Controllers\EventManager\Accommodation;

use App\Accessors\AccessControl;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use Illuminate\Contracts\Support\Renderable;
use MetaFramework\Traits\Responses;

class GroupController extends Controller
{
    use Responses;

    public function edit(Event $event, Accommodation $accommodation): Renderable
    {
        return view('events.manager.accommodation.groups')->with([
            'event' => $event,
            'accommodation' => $accommodation,
            'groups' => $event->eventGroups()->whereHas('blockedRooms')->with('group')->get()->mapWithKeys(function ($eventGroup) {
                return [$eventGroup->id => $eventGroup->group->name];
            })->sort()->toArray(),
        ]);
    }
}
