<?php

namespace App\Http\Controllers\EventManager\EventGroup;

use App\Accessors\EventManager\EventGroups;
use App\DataTables\EventGroupContactDataTable;
use App\DataTables\EventGroupDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GroupController;
use App\Http\Requests\EventManager\EventGroup\EventGroupRequest;
use App\Models\Event;
use App\Models\EventManager\EventGroup;
use App\Models\Order;
use App\Models\Order\Invoiceable;
use App\Traits\DataTables\MassDelete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use Throwable;

class EventGroupController extends Controller
{
    use Responses;
    use MassDelete;

    public function index(Event $event): JsonResponse|View
    {
        $dataTable = new EventGroupDataTable($event);

        return $dataTable->render('events.manager.event_group.index', [
            "event"     => $event,
            "dataTable" => $dataTable,
        ]);
    }

    public function eventGroupContactDatatableData(Event $event, EventGroup $eventGroup)
    {
        $dataTable = $this->getEventGroupContactDatatable($event, $eventGroup);

        return $dataTable->ajax();
    }


    public function edit(Event $event, EventGroup $eventGroup)
    {
        if ($eventGroup->event_id != $event->id) {
            return view('errors.back-office-event')->with([
                'event' => $event,
                'message' => "Groupe non associé à cet évènement
                ",
            ]);
        }

        $eventGroupContactDatatable = $this->getEventGroupContactDatatable($event, $eventGroup);

        $event->load('accommodation.hotel', 'accommodation.roomGroups', 'accommodation.contingent', 'accommodation.blocked', 'accommodation.grant');

        $eventGroupAccessor = (new EventGroups)->setEventGroup($eventGroup)->setEvent($event);
        $contacts = $eventGroupAccessor->getEventContacts()->load('account');

        return view('events.manager.event_group.edit', [
            'redirect_to'                => route('panel.manager.event.event_group.index', [
                'event' => $event,
            ]),
            'event'                      => $event,
            'eventGroup'                 => $eventGroup,
            'eventGroupContactDatatable' => $eventGroupContactDatatable->html(),
            'hotels'                     => $event->accommodation,
            'orders'                     => (new EventGroups)->setEventGroup($eventGroup)->setEvent($event)->getEventGroupOrders(),
            'services'                   => $event->sellableService->load('event.services'),
            'eventGroupContacts'          => $contacts,
        ])->with((new GroupController())->getGroupEditViewData($eventGroup->group, $eventGroup));
    }

    public function update(EventGroupRequest $request, Event $event, EventGroup $eventGroup): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $eventGroup->update($validated);
            $this
                ->responseSuccess("Le participant a bien été mis à jour.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function destroy(Event $event, EventGroup $eventGroup): RedirectResponse
    {
        return (new Suppressor($eventGroup))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le groupe est bien dissocié de l\'événément.'))
            ->redirectTo(
                route('panel.manager.event.event_group.index', [
                    "event" => $event,
                ]),
            )
            ->sendResponse();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getEventGroupContactDatatable(Event $event, EventGroup $eventGroup): EventGroupContactDataTable
    {
        return new EventGroupContactDataTable($event, $eventGroup);
    }
}
