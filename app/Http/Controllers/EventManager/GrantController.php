<?php

namespace App\Http\Controllers\EventManager;

use App\Accessors\Dictionnaries;
use App\Accessors\EventAccessor;
use App\Accessors\EventManager\EventDepositStats;
use App\Actions\EventManager\GrantActions;
use App\DataTables\EventGrantDataTable;
use App\DataTables\EventGrantRecapDataTable;
use App\DataTables\View\EventGrantView;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\GrantRequest;
use App\Models\Event;
use App\Models\EventManager\Grant\{Address, Domain, Establishment, Grant, GrantLocation, ParticipationType, Profession};
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Validation\ValidationTrait;
use Throwable;

class GrantController extends Controller
{
    use ValidationTrait;

    private Grant $model;
    private Event $event;

    public function index(Event $event): JsonResponse|View
    {
        $dataTable = new EventGrantDataTable($event);
        $deposits  = new EventDepositStats($event);

        return $dataTable->render('events.manager.grant.datatable.index', [
            'event'       => $event,
            'deposits'    => $deposits,
            'statusOrder' => $deposits->getStatusOrder(),
        ]);
    }

    public function create(Event $event): Renderable
    {
        return view('events.manager.grant.edit')->with(
            array_merge([
                'data'    => new Grant(),
                'address' => new Address(),
                'route'   => route('panel.manager.event.grants.store', $event),
            ], $this->sharedData($event)),
        );
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(GrantRequest $request, Event $event): RedirectResponse
    {
        try {
            $this->event           = $event;
            $this->model           = new Grant($this->castedData($request->validated('grant')));
            $this->model->event_id = $event->id;
            $this->model->save();

            $this->process();
            $this->redirect_to = route('panel.manager.event.grants.edit', ['event'=>$event, 'grant' => $this->model]);
            $this->responseSuccess(__('mfw.record_created'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        $this->tabRedirect();

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, Grant $grant): Renderable
    {
        $eventGrantView = EventGrantView::find($grant->id);

        return view('events.manager.grant.edit')->with(
            array_merge([
                'eventGrantView' => $eventGrantView,
                'data'    => $grant,
                'address' => $grant->address,
                'recap'   => $this->getGrantRecapDataTable($grant)->html(),
                'route'   => route('panel.manager.event.grants.update', ['event' => $event->id, 'grant' => $grant->id]),
            ], $this->sharedData($event)),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GrantRequest $request, Event $event, Grant $grant): RedirectResponse
    {
       // try {
            $this->event = $event;
            $this->model = $grant;
            $this->model->update($this->castedData($request->validated('grant')));
            $this->process();
            $this->redirect_to = route('panel.manager.event.grants.edit', ['event'=>$event, 'grant' => $this->model]);
            $this->responseSuccess(__('mfw.record_updated'));
      /*  } catch (Throwable $e) {
            $this->responseException($e);
        } */

        $this->tabRedirect();

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Event $event, Grant $grant)
    {
        return (new Suppressor($grant))
            ->remove()
            ->redirectTo(route('panel.manager.event.grants.index', $event))
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object')
            ->sendResponse();
    }

    public function recap(Event $event, Grant $grant): JsonResponse|View
    {
        $dataTable = new EventGrantRecapDataTable($grant);

        return $dataTable->render('events.manager.grant.stats.recap', [
            'event' => $event,
            'grant' => $grant,
        ]);
    }

    private function processAddress(): void
    {
        $this->model->address()->updateOrCreate(
            ['grant_id' => $this->model->id],
            request('wa_geo'),
        );
    }

    private function castedData(array $data): array
    {
        $data['prenotification_date']     = Carbon::createFromFormat('d/m/Y', $data['prenotification_date'])->toDateString();
        $data['manage_transport_upfront'] = $data['manage_transport_upfront'] ?? null;
        $data['manage_transfert_upfront'] = $data['manage_transfert_upfront'] ?? null;
        $data['refund_transport']         = $data['refund_transport'] ?? null;
        $data['active']                   = $data['active'] ?? null;

        return $data;
    }

    private function process(): void
    {
        $this->processAddress();
        $this->processContact();
        $this->processDomains();
        $this->processParticipationTypes();
        $this->processProfessions();
        $this->processLocations();
        $this->processEstablishments();

        $this->pushMessages(
            (new GrantActions())->updateEligibleStatusForContacts($this->event),
        );


        $this->redirectTo(route('panel.manager.event.grants.edit', ['event' => $this->event->id, 'grant' => $this->model->id]));
    }

    private function processContact(): void
    {
        $this->model->contact()->updateOrCreate(
            ['grant_id' => $this->model->id],
            request('grant_contact'),
        );
    }

    private function processDomains(): void
    {
        $this->model->domains()->delete();

        $domains = request('grant_domains');
        if ($domains) {
            $models = [];
            foreach ($domains as $itemId => $info) {
                $active   = $info['active'] ?? null;
                $nbPec    = $info['pax'] ?? null;
                $models[] = new Domain([
                    'domain_id' => $itemId,
                    'pax'       => $nbPec,
                    'active'    => $active,
                ]);
            }
            $this->model->domains()->saveMany($models);
        }
    }

    private function processParticipationTypes(): void
    {
        $items = request('grant_participation_types');
        if ($items) {
            for ($i = 0; $i < count($items['id']); $i++) {
                $data = [
                    'pax'              => $items['pax'][$i] ?: null,
                    'active'           => $items['is_active'][$i] ?: null,
                    'participation_id' => $items['participation_id'][$i],
                ];
                $items['id'][$i]
                    ? ParticipationType::where('id', $items['id'][$i])->update($data)
                    :
                    $this->model->participationTypes()->save(new ParticipationType($data));
            }
        }
    }

    private function processProfessions(): void
    {
        $this->model->professions()->delete();

        $items = request('grant_profession');
        if ($items) {
            $models = [];
            foreach ($items as $itemId => $info) {
                $active   = $info['active'] ?? null;
                $nbPec    = $info['pax'] ?? null;
                $models[] = new Profession([
                    'profession_id' => $itemId,
                    'pax'           => $nbPec,
                    'active'        => $active,
                ]);
            }
            $this->model->professions()->saveMany($models);
        }
    }

    private function processLocations(): void
    {
        $items = request('grant_binded_location');

        if ($items) {
            for ($i = 0; $i < count($items['amount']); $i++) {
                $data = [
                    'locality'     => $items['locality'][$i],
                    'country_code' => $items['country_code'][$i],
                    'type'         => $items['type'][$i],
                    'continent'    => $items['continent'][$i],
                    'pax'          => $items['pax'][$i],
                    'amount'       => $items['amount'][$i],
                ];

                $items['id'][$i]
                    ? GrantLocation::where('id', $items['id'][$i])->update($data)
                    :
                    $this->model->locations()->save(new GrantLocation($data));
            }
        }
    }

    private function processEstablishments(): void
    {
        $this->model->establishments()->delete();
        $items = request('grant_establishment');

        if ($items) {
            $models = [];
            for ($i = 0; $i < count($items['establishment_id']); ++$i) {
                $models[] = new Establishment([
                    'establishment_id' => $items['establishment_id'][$i],
                    'pax'              => $items['pax'][$i],
                ]);
            }
            $this->model->establishments()->saveMany($models);
        }
    }

    private function sharedData(Event $event): array
    {
        return [
            'event'         => $event,
            'eventAccessor' => new EventAccessor($event),
            'domains'       => Dictionnaries::dictionnary('domain')->entries->pluck('name', 'id'),
            'professions'   => Dictionnaries::dictionnary('professions'),
        ];
    }

    public function grantRecapDatatableData(Event $event, Grant $grant)
    {
        return $this->getGrantRecapDataTable($grant)->ajax();
    }

    private function getGrantRecapDataTable(Grant $grant): EventGrantRecapDataTable
    {
        return new EventGrantRecapDataTable($grant);
    }
}
