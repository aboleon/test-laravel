<?php

namespace App\Http\Controllers\EventManager\EventContact;

use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Accessors\Order\Orders;
use App\Actions\EventManager\GrantActions;
use App\Dashboards\EventContactsSecondaryDashboardFilter;
use App\DataTables\EventContactDashboardChoosableDataTable;
use App\DataTables\EventContactDashboardInterventionDataTable;
use App\DataTables\EventContactDashboardSessionDataTable;
use App\DataTables\EventContactDashboardTransportDataTable;
use App\DataTables\EventContactDataTable;
use App\DataTables\EventContactPecDataTable;
use App\DataTables\View\EventContactView;
use App\DataTables\View\EventDepositView;
use App\Enum\RegistrationType;
use App\Enum\SavedSearches;
use App\Events\EventContactPecUpdated;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\EventContact\EventContactRequest;
use App\Models\AdvancedSearchFilter;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\Group;
use App\View\Components\EventContactsSecondaryFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use ReflectionClass;
use Throwable;

class EventContactController extends Controller
{
    use Responses;

    public function index(Event $event, string $group, ?string $withOrder = null): JsonResponse|View
    {
        $secondaryFilter = (string)request('secondaryFilter');
        $filteredIds = [];

        $groupInstance = null;

        if ((int)request('group_id')) {
            $groupInstance = Group::findOrFail(request('group_id'));
        }

        if ($group == 'industry' && !$secondaryFilter) {
            $withOrder = 'yes';
        }

        # Les Accès rapides
        if ($secondaryFilter) {
            $filteredIds = new EventContactsSecondaryDashboardFilter($secondaryFilter)->setEvent($event)->run()->getIds();
        }


        $dataTable = new EventContactDataTable($event, $group, $withOrder, $filteredIds);

        if($group=='all') {
            $sessionCount['all'] = EventContactView::where('event_id', $event->id)->count();
            $sessionCountWebRegister['all'] = EventContactView::where('event_id', $event->id)->where('registration_type', '!=', '')->count();
        }
        else {
            $sessionCount[$group] = EventContactView::where('event_id', $event->id)->where('participation_type_group', '=', $group)->count();
            $sessionCountWebRegister[$group] = EventContactView::where('event_id', $event->id)->where('participation_type_group', '=', $group)->where('registration_type', '!=', '')->count();
        }

        $sessionCountwithOrder = EventContactView::where('event_id', $event->id)->where('nb_orders', '>', 0)->count();
        $sessionCountwithoutOrder = EventContactView::where('event_id', $event->id)->where('nb_orders', '=', 0)->count();

        return $dataTable->render('events.manager.event_contact.index', [
            'secondaryFilter' => $secondaryFilter,
            'searchFilters' => AdvancedSearchFilter::getFilters(SavedSearches::EVENT_CONTACTS->value),
            'withOrder' => $withOrder,
            "event"     => $event,
            "dataTable" => $dataTable,
            "group"     => $groupInstance,
            "groupType" => $group,
            "sessionCount" => $sessionCount,
            "sessionCountwithOrder" => $sessionCountwithOrder,
            "sessionCountwithoutOrder" => $sessionCountwithoutOrder,
            "sessionCountWebRegister" => $sessionCountWebRegister
        ]);
    }

    public function indexWithOrder(Event $event, string $group, string $withOrder)
    {
        return $this->index($event, $group, $withOrder);
    }

    public function transportDatatableData(Event $event, EventContact $eventContact)
    {
        $dataTable = $this->getTransportDataTable($event, $eventContact);

        return $dataTable->ajax();
    }

    public function interventionDatatableData(Event $event, EventContact $eventContact)
    {
        $dataTable = $this->getInterventionDataTable($event, $eventContact);

        return $dataTable->ajax();
    }

    public function sessionDatatableData(Event $event, EventContact $eventContact)
    {
        $dataTable = $this->getSessionDataTable($event, $eventContact);

        return $dataTable->ajax();
    }

    public function choosableDatatableData(Event $event, EventContact $eventContact)
    {
        $dataTable = $this->getChoosableDataTable($event, $eventContact);

        return $dataTable->ajax();
    }


    public function edit(Event $event, EventContact $eventContact)
    {
        if ($eventContact->event_id != $event->id) {
            return view('errors.back-office-event')->with([
                'event'   => $event,
                'message' => "Participant non associé à cet évènement
                ",
            ]);
        }

        $eventContactAccessor = new EventContactAccessor()
            ->setEventContact($eventContact)
            ->setEvent($event);

        $transportDataTable    = $this->getTransportDataTable($event, $eventContact);
        $interventionDataTable = $this->getInterventionDataTable($event, $eventContact);
        $sessionDataTable      = $this->getSessionDataTable($event, $eventContact);
        $choosableDataTable    = $this->getChoosableDataTable($event, $eventContact);
        $pecDataTable          = $this->getPecDataTable($event, $eventContact);

        if ( ! $eventContact->account) {
            abort(404, "Vous essayez d'accéder à la fiche Participant d'un compte archivé");
        }

        return view('events.manager.event_contact.edit', [
            'eventContactAccessor'  => $eventContactAccessor,
            'accountAccessor'       => new Accounts($eventContact->account),
            'redirect_to'           => route('panel.manager.event.event_contact.index', [
                'event' => $event,
                'group' => $eventContact->participationType?->group ?? 'all',
            ]),
            'event'                 => $event,
            'eventContact'          => $eventContact,
            'hasTransport'          => $eventContactAccessor->hasTransport(),
            'transportDataTable'    => $transportDataTable->html(),
            'interventionDataTable' => $interventionDataTable->html(),
            'sessionDataTable'      => $sessionDataTable->html(),
            'pecDataTable'          => $pecDataTable->html(),
            'choosableDataTable'    => $choosableDataTable->html(),
            'attributed'            => $eventContactAccessor->getAttributionSummary(),
            'orders'                => Orders::getEventDashboardOrders($event, $eventContact),
            'services'              => $event->sellableService->load('event.services'),
            'deposits'              => EventDepositView::where('event_id', $event->id)
                ->where('event_contact_id', $eventContact->id)
                ->orderBy('id', 'desc')->get(),
        ])->with(new AccountController()->getAccountEditViewData($eventContact->user->account));
    }

    public function update(EventContactRequest $request, Event $event, EventContact $eventContact): RedirectResponse
    {
        $exemptFromDeposit = request()->has('no_deposit');

        try {
            $validated = $request->validated();

            switch (request('section')) {
                case 'general':
                    $participationTypeId = (int)$validated['participation_type_id'];
                    if ( ! $participationTypeId) {
                        $this->responseError("Le type de participation est invalide.");
                    }
                    break;

                case 'dashboard':
                    $validated['order_cancellation'] = $validated['order_cancellation'] ?? null;
                    break;

                case 'pec':
                    $validated['pec_enabled']    = $validated['pec_enabled'] ?? null;
                    $validated['pec_fees_apply'] = request()->has('no_pec_fee') ? null : 1;
                    break;

                default:
                    $this->responseError("Le type de section est invalide.");
            }

            if ($exemptFromDeposit) {
                $validated['grant_deposit_not_needed'] = 1;
            }

            $grantActions = (new GrantActions());
            $this->pushMessages(
                $grantActions->updateEligibleStatusForSingleContact($event, $eventContact),
            );

            if (isset($validated['pec_enabled']) && $validated['pec_enabled'] == 1 && ! $exemptFromDeposit) {
                $this->pushMessages(
                    $grantActions->attachDepositToEventContact((array)request('preferred_grant'), $eventContact),
                );

                if ($grantActions->hasErrors()) {
                    $validated['pec_enabled'] = null;
                }
            }

            $eventContact->update($validated);

            event(new EventContactPecUpdated($eventContact));

            $this->redirect_to = route('panel.manager.event.event_contact.index', [
                'event' => $event,
                'group' => $eventContact->participationType?->group ?? 'all',
            ]);
            $this->responseSuccess("Le participant a bien été mis à jour.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function destroy(Event $event, EventContact $eventContact): RedirectResponse
    {
        if (
            ! new EventContactAccessor()
                ->setEventContact($eventContact)
                ->setEvent($event)
                ->isDeletable()
        ) {
            $this->responseError("Ce participant ne peut pas être supprimé car des éléments sont liés à son compte (commandes, transports, interventions, sessions ou invitations).");

            return $this->sendResponse();
        }

        $group = match ($eventContact->registration_type) {
            RegistrationType::CONGRESS->value => 'congress',
            RegistrationType::INDUSTRY->value => 'industry',
            RegistrationType::ORATOR->value => 'orator',
            default => 'all',
        };

        return new Suppressor($eventContact)
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le contact est bien dissocié de l\'événément.'))
            ->redirectTo(
                route('panel.manager.event.event_contact.index', [
                    "event" => $event,
                    "group" => $group,
                ]),
            )
            ->sendResponse();
    }

    public function massDelete(Request $request, string $name = 'name'): array
    {
        $modelPath      = $request->get('model_path', $request->get('model'));
        $deletedMessage = $request->get('deleted_message');

        $this->enableAjaxMode();

        if ( ! $request->filled('ids')) {
            $this->responseWarning("Aucun identifiant n'a été fourni pour suppression.");

            return $this->fetchResponse();
        }

        try {
            $model = new ReflectionClass('\App\Models\\'.$modelPath)->newInstance();
        } catch (Throwable $e) {
            $this->responseException($e, $request->get('model')." n'est pas une class valide.");

            return $this->fetchResponse();
        }

        $ids = explode(',', $request->get('ids'));

        $items = EventContactView::whereIn('id', $ids)->where('has_something', '=', 0)->get()->pluck($name, 'id')->toArray();

        foreach ($items as $key => $item) {
            try {
                $model->query()->where('id', $key)->delete();
                $this->responseSuccess($deletedMessage ?? (($item ?? "L'élément").' a été supprimé'));
            } catch (Throwable $e) {
                $this->responseException($e, 'Une erreur est survenue sur la tentative de suppression de '.($item ?? 'l\'id'.$key).'. Ligne non supprimée.');
            }
        }
        $this->responseNotice("Seulement les contacts n'ayant aucune commande peuvent être dissociés.");

        return $this->fetchResponse();
    }


    private function getTransportDataTable(Event $event, EventContact $eventContact): EventContactDashboardTransportDataTable
    {
        $dataTable = new EventContactDashboardTransportDataTable($event);
        $dataTable->setEventContactId($eventContact->id);
        $dataTable->setRoute(route('panel.manager.event.event_contact.dashboard.transport', ['event' => $event->id, 'eventContact' => $eventContact->id]));

        return $dataTable;
    }

    private function getInterventionDataTable(Event $event, EventContact $eventContact): EventContactDashboardInterventionDataTable
    {
        $dataTable = new EventContactDashboardInterventionDataTable($event, $eventContact);

        return $dataTable;
    }

    private function getPecDataTable(Event $event, EventContact $eventContact): EventContactPecDataTable
    {
        return new EventContactPecDataTable($event, $eventContact);
    }

    public function pecDatatableData(Event $event, EventContact $eventContact)
    {
        $dataTable = $this->getPecDataTable($event, $eventContact);

        return $dataTable->ajax();
    }

    private function getSessionDataTable(Event $event, EventContact $eventContact): EventContactDashboardSessionDataTable
    {
        $dataTable = new EventContactDashboardSessionDataTable($event, $eventContact);

        return $dataTable;
    }

    private function getChoosableDataTable(Event $event, EventContact $eventContact): EventContactDashboardChoosableDataTable
    {
        $dataTable = new EventContactDashboardChoosableDataTable($event, $eventContact);

        return $dataTable;
    }
}
