<?php

namespace App\Http\Controllers\EventManager;

use App\Accessors\Dictionnaries;
use App\Accessors\EventManager\Availability;
use App\Accessors\Order\RoomingListAccessor;
use App\DataTables\EventAccommodationDataTable;
use App\Exports\RoomingListExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\AccommodationRequest;
use App\Http\Requests\EventManager\AccommodationServiceRequest;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Accommodation\Deposit;
use App\Traits\DataTables\MassDelete;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Validation\ValidationTrait;
use Throwable;

class AccommodationController extends Controller
{
    use MassDelete;
    use ValidationTrait;

    public function index(Event $event): JsonResponse|View
    {
        $dataTable = new EventAccommodationDataTable($event);

        return $dataTable->render('events.manager.accommodation.datatable.index', ['event' => $event]);
    }

    public function show(Event $event, Accommodation $accommodation): Renderable
    {
        return view('events.manager.accommodation.dashboard')->with([
            'event' => $event,
            'accommodation' => $accommodation,
            'availability' => (new Availability())->setEventAccommodation($accommodation),
            'eventGroups' => $event->eventGroups->load('group')->mapWithKeys(fn($item) => [$item->id => $item->group->name]),
            'participations' => Dictionnaries::participationTypes()->flatten(),
            'orators' => Dictionnaries::orators(),
        ]);
    }

    public function edit(Event $event, Accommodation $accommodation)
    {
        return view('events.manager.accommodation.edit')->with([
            'accommodation' => $accommodation,
            'event' => $event,
            'participations' => Dictionnaries::participationTypes()
            //Dictionnaries::filterAgainstMetaType($participation->entries, ($event->serialized_config['event_participations'] ?? [])),
        ]);
    }

    public function update(AccommodationRequest $request, Event $event, Accommodation $accommodation): RedirectResponse
    {
        try {

            // Update Accommodation

            $data = $request->validated('accommodation');
            $data['pec'] = request()->has($request->getPrefix() . 'pec') ? 1 : null;
            $data['published'] = request()->has($request->getPrefix() . 'published') ? now() : null;
            $data['participation_types'] = request()->filled($request->getPrefix() . 'participation_types')
                ? implode(',', request($request->getPrefix() . 'participation_types'))
                : null;
            $data['vat_id'] = request($request->getPrefix() . 'vat_id');

            $accommodation->update($data);

            // Update Event
            $service_prefix = (new AccommodationServiceRequest())->getPrefix();
            $service = $request->validated('service');
            $service['participation_types'] = request()->filled($service_prefix . 'participation_types')
                ? implode(',', request($service_prefix . 'participation_types'))
                : null;

            $accommodation->service()->updateOrCreate(
                ['event_accommodation_id' => $accommodation->id],
                $service);

            try {
                $accommodation->deposits()->delete();

                if ($request->validated('deposit')) {
                    $d = $request->validated('deposit');
                    for ($i = 0; $i < count($d['amount']); ++$i) {
                        $accommodation->deposits()->save(new Deposit([
                            'amount' => $d['amount'][$i],
                            'paid_at' => Carbon::createFromFormat('d/m/Y', $d['paid_at'][$i])
                        ]));
                    }
                }
            } catch (Throwable $e) {
                $this->responseException($e, "Un problème est survenu lors de l'enregistrement des acomoptes");
            }

            $this->responseSuccess(__('ui.record_updated'));
            $this->saveAndRedirect(route('panel.manager.event.accommodation.index', $event));

        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function destroy(Event $event, Accommodation $accommodation): RedirectResponse
    {
        if ($accommodation->bookings->count()) {
            $this->responseWarning("Vous ne pouvez pas dissocier cet hébergement car il a " . $accommodation->bookings->count() . " réservations.");
            return $this->sendResponse();
        }

        return (new Suppressor($accommodation))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__("L'hôtel est bien dissocié de l'événement."))
            ->redirectTo(route('panel.manager.event.accommodation.index', [
                "event" => $event,
            ]))
            ->sendResponse();
    }

    public function exportRoomingList(Event $event, Accommodation $accommodation)
    {
        $filename = 'rooming_list_event_' . $event->id . '_hotel_' . $accommodation->id . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new RoomingListExport($accommodation), $filename);
    }

    public function reportRoomingList(Event $event, Accommodation $accommodation): Renderable
    {
       $roomingList =  (new RoomingListAccessor($accommodation));
       return view('reports.rooming-list', [
           'event' => $event,
           'accommodation' => $accommodation,
           'data' => $roomingList->getData(),
           'roomingList' => $roomingList,
           'with_order' => true
       ]);
    }



}
