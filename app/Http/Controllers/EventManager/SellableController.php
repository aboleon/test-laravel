<?php

namespace App\Http\Controllers\EventManager;

use App\Accessors\Dictionnaries;
use App\Accessors\EventManager\SellableAccessor;
use App\Accessors\Places;
use App\Actions\EventManager\SellableDeposit;
use App\Actions\EventManager\SellableOption;
use App\Actions\EventManager\SellablePrice;
use App\DataTables\EventSellableDataTable;
use App\DataTables\EventSellableSalesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\SellableServiceRequest;
use App\Models\Event;
use App\Models\EventManager\Sellable;
use App\Traits\DataTables\MassDelete;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use Throwable;

class SellableController extends Controller
{
    use MassDelete;
    use Responses;

    private Sellable $sellable;

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event): JsonResponse|View
    {
        $dataTable = new EventSellableDataTable($event);

        return $dataTable->render('events.manager.sellable.datatable.index', ['event' => $event]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event): Renderable
    {
        return view('events.manager.sellable.edit')->with([
            'data'             => new Sellable(),
            'places'           => Places::selectableArray(),
            'sellableAccessor' => new SellableAccessor(new Sellable()),
            'event'            => $event,
            'route'            => route('panel.manager.event.sellable.store', $event),
            'professions'      => Dictionnaries::dictionnary('professions'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SellableServiceRequest $request, Event $event): RedirectResponse
    {
        try {
            $this->sellable           = new Sellable();
            $this->sellable->event_id = $event->id;
            $this->processModel($request);
            $this->sellable->stock = (int)request('service.stock');
            $this->sellable->save();

            $this->syncPivots();
            $this->syncRelations();

            // Caution
            (new SellableDeposit(sellable: $this->sellable, request: $request, store: true))();

            $this->redirect_to = route('panel.manager.event.sellable.edit', ['event' => $event, 'sellable' => $this->sellable]);
            $this->responseSuccess("La prestation a été ajoutée.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        $this->tabRedirect();

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, Sellable $sellable): Renderable
    {
        if (request('mfw_tab')) {
            session()->flash('mfw_tab_redirect', request('mfw_tab'));
        }

        return view('events.manager.sellable.edit')->with([
            'data'             => $sellable,
            'places'           => Places::selectableArray(),
            'sellableAccessor' => new SellableAccessor($sellable),
            'recap'            => $this->getSalesRecapDataTable($sellable)->html(),
            'event'            => $event,
            'route'            => route('panel.manager.event.sellable.update', [$event, $sellable]),
            'professions'      => Dictionnaries::dictionnary('professions'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SellableServiceRequest $request, Event $event, Sellable $sellable): RedirectResponse
    {
        $stockUnlimited = $request->input('service.stock_unlimited', false);

        $sellableAccessor = (new SellableAccessor($sellable));
        $booked           = $sellableAccessor->getBookingsCount();

        if ( ! $stockUnlimited) {
            if ((int)request('service.stock') < $booked) {
                $this->responseError("Le stock total ne peut pas être inférieur aux achats déjà effectués.");

                return $this->sendResponse();
            }
        }

        try {
            $this->sellable = $sellable;
            $this->processModel($request);
            $this->syncPivots();
            $this->syncRelations();

            // Caution
            (new SellableDeposit(sellable: $this->sellable, request: $request))();

            $this->redirect_to = route('panel.manager.event.sellable.edit', ['event' => $event, 'sellable' => $this->sellable]);
            $this->responseSuccess(__('ui.record_updated'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }
        $this->tabRedirect();

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Event $event, Sellable $sellable)
    {
        return (new Suppressor($sellable))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('La prestation est supprimée.'))
            ->redirectTo(route('panel.manager.event.sellable.index', $event))
            ->sendResponse();
    }


    private function processModel(SellableServiceRequest $request)
    {
        $service = $request->validated('service');
        $texts   = $request->get('service_texts');

        $this->sellable->published                   = $service['published'] ?? null;
        $this->sellable->invitation_quantity_enabled = $service['invitation_quantity_enabled'] ?? null;
        $this->sellable->is_invitation               = $service['is_invitation'] ?? null;
        $this->sellable->pec_eligible                = $service['pec'] ?? null;
        $this->sellable->pec_max_pax                 = $service['pec_max_pax'] ?: 1;
        $this->sellable->service_group               = $service['service_group'];
        $this->sellable->service_group_combined      = $service['service_group_combined'] != $service['service_group'] ? $service['service_group_combined'] : null;
        $this->sellable->service_date                = $service['service_date'];
        $this->sellable->service_starts              = $service['service_starts'];
        $this->sellable->service_ends                = $service['service_ends'];
        $this->sellable->place_id                    = $service['place_id'];
        $this->sellable->room_id                     = $service['room_id'];
        $this->sellable->vat_id                      = $service['vat_id'];
        $this->sellable->stock_showable              = $service['stock_showable'];
        $this->sellable->stock_unlimited             = request()->has('service.stock_unlimited') ? 1 : null;
        $this->sellable->stock                       = (int)request('service.stock');
        $this->sellable->title                       = $texts['title'];
        $this->sellable->description                 = $texts['description'];
        $this->sellable->vat_description             = $texts['vat_description'];


        $this->sellable->save();
    }


    private function syncPivots(): void
    {
        // Types de participations
        $this->sellable->participations()->sync((array)request('service_participations'));
        // Types de professions
        $this->sellable->professions()->sync((array)request('service_professions'));
    }

    private function syncRelations(): void
    {
        // Options
        (new SellableOption(sellable: $this->sellable))();
        // Prix
        (new SellablePrice(sellable: $this->sellable))();
    }

    public function salesRecapDatatableData(Event $event, Sellable $sellable)
    {
        return $this->getSalesRecapDataTable($sellable)->ajax();
    }

    private function getSalesRecapDataTable(Sellable $sellable): EventSellableSalesDataTable
    {
        return new EventSellableSalesDataTable($sellable);
    }
}
