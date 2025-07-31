<?php

namespace App\Http\Controllers;

use App\Accessors\{Dictionnaries, EventAccessor, Places};
use App\Actions\EventManager\GrantActions;
use App\DataTables\EventDataTable;
use App\Http\Requests\EventRequest;
use App\Models\{Event, EventManager\Grant\Domain, EventManager\Grant\Grant, EventManager\Grant\ParticipationType, EventService, EventShoppingRanges, EventTexts, Sage, Sellable, User};
use App\Printers\EventPrinter;
use App\Traits\DataTables\MassDelete;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Casts\BooleanNull;
use MetaFramework\Services\Validation\ValidationTrait;
use Throwable;

class EventController extends Controller
{
    use MassDelete;
    use ValidationTrait;

    private array $config_data;

    private Event $event;

    /**
     * Display a listing of the resource.
     */
    public function index(EventDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render('events.index', [
            'archived' => request()->routeIs('panel.events.archived'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $event = new Event();

        return view('events.edit')->with(
            array_merge(
                $this->sharedEditableData($event),
                [
                    'route'    => route('panel.events.store'),
                    'contacts' => [],
                ],
            ),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request): RedirectResponse
    {
        $this->tabRedirect();

        $this->setConfigData();
        $this->event = Event::create($this->config_data);
        $this->event->texts()->create(request('event.texts'));
        $this->event->pec()->create(request('event.pec'));
        $this->event->shop()->create(request('event.shop'));
        $this->event->frontConfig()->create(request('event.front_config'));

        $this->event->processMedia();
        $this->syncPivots();
        $this->syncShopRanges();

        $this->successActions($this->event);
        $this->redirect_to = route('panel.events.edit', $this->event);

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $event_id): Renderable
    {
        $event = Event::withTrashed()->findOrFail($event_id);

        return view('events.edit')->with(
            array_merge(
                $this->sharedEditableData($event),
                [
                    'route'    => route('panel.events.update', $event),
                    'contacts' => (new EventAccessor($event))->clients(),
                ],
            ),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        //de(request()->all());
        $this->tabRedirect();
        $this->event = $event;

        $this->setConfigData();

        $pec_data              = request('event.pec');
        $pec_data['is_active'] = request()->has('event.pec.is_active');

        $this->syncContactEligibility($event, $pec_data['is_active']);


        $this->event->update($this->config_data);
        $this->event->texts()->update(request('event.texts'));
        $this->event->pec()->update($pec_data);
        $shop_data = request('event.shop');
        if ( ! request()->has('event.shop.is_active')) {
            $shop_data['is_active'] = 0;
        }
        $this->event->shop()->update($shop_data);

        $frontConfigData                     = request('event.front_config');
        $frontConfigData['speaker_pay_room'] = $frontConfigData['speaker_pay_room'] ?? null;
        $this->event->frontConfig()->update($frontConfigData);

        $this->event->processMedia();
        $this->syncPivots();
        $this->syncShopRanges();

        $this->successActions($this->event);

        $this->redirect_to = route('panel.events.edit', $this->event);


        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Event $event): RedirectResponse
    {
        // TODO: enlever car Event utilise SoftDeletes
        $event->media()->delete();

        return (new Suppressor($event))
            ->remove()
            ->redirectRoute('panel.events.index')
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object')
            ->sendResponse();
    }

    /**
     * @param  Event  $event
     *
     * @return array<string, mixed>
     */
    private function sharedEditableData(Event $event): array
    {
        if ($event->id) {
            $event->load(['texts', 'frontConfig']);
        }

        $participation     = Dictionnaries::participationTypes();
        $catalog           = Dictionnaries::dictionnary('catalog');
        $domains           = Dictionnaries::dictionnary('domain');
        $sellables         = Sellable::with(['category', 'vat'])->get();
        $sellables_grouped = $sellables->sortBy('category.name')->groupBy('category.id');

        return [
            'data'                    => $event,
            'sellables'               => $sellables,
            'sellables_grouped'       => $sellables_grouped,
            'texts'                   => $event->texts ?: new EventTexts(),
            'places'                  => Places::selectableArray(),
            'accessor'                => (new EventAccessor($event)),
            'printer'                 => (new EventPrinter($event)),
            'professions'             => Dictionnaries::dictionnary('professions'),
            'participation'           => $participation,
            'catalog'                 => $catalog,
            'assigned_participations' => $participation->flatten(1)->filter(fn($item) => in_array($item->id, ($event->serialized_config['event_participations'] ?? [])))->pluck('id'),
            'orators'                 => Dictionnaries::orators(),
            'domains'                 => $domains,
            'assigned_domains'        => Dictionnaries::filterAgainstSimpleType($domains->entries, $event->domains->pluck('id')->toArray()),
            'services'                => Dictionnaries::dictionnary('service_family'),
            'admin_users'             => User::withRole('super-admin')->selectRaw('id, CONCAT_WS(" ", first_name, last_name) as username')->pluck('username', 'id')->toArray(),
            'media_settings'          => $event->mediaSettings(),
        ];
    }

    private function successActions($event): void
    {
        $this->responseSuccess(__('mfw.record_created'));
        $this->redirectTo(route('panel.events.edit', $event));
        $this->saveAndRedirect(route('panel.events.index'));
    }

    private function setConfigData()
    {
        $this->config_data = request('event.config');
        $booleans          = array_filter((new Event())->getCasts(), fn($item) => $item == BooleanNull::class);

        foreach ($booleans as $key => $value) {
            $this->config_data[$key] = $this->config_data[$key] ?? null;
        }

        if (request()->has('event_catalog')) {
            $this->config_data['serialized_config']['event_catalog'] = request('event_catalog');
        }

        $this->config_data['transport'] = request()->has('event.transport') ? array_keys(request('event.transport')) : null;
        $this->config_data['transfert'] = request()->has('event.transfert') ? array_keys(request('event.transfert')) : null;
    }


    private function syncPivots(): void
    {
        $this->syncEventServices();
        $this->event->shopDocs()->sync((array)request('shop_docs'));
        $this->event->domains()->sync((array)request('event_domains'));
        $this->event->pecDomains()->sync((array)request('pec_domains'));
        $this->event->participations()->sync(array_filter((array)request('event_participations'), fn($item) => is_numeric($item)));
        $this->event->pecParticipations()->sync((array)request('pec_participations'));
        $this->event->professions()->sync((array)request('event_professions'));
        $this->event->orators()->sync((array)request('event_orators'));


        //--------------------------------------------
        // Remove domain and participation types from grants
        //--------------------------------------------
        $pecDomainIds        = $this->event->pecDomains->pluck('id')->toArray();
        $pecParticipationIds = $this->event->pecParticipations->pluck('id')->toArray();
        $this->event->grants->each(function (Grant $grant) use ($pecDomainIds, $pecParticipationIds) {
            foreach ($grant->domains as $domain) {
                if ( ! in_array($domain->domain_id, $pecDomainIds)) {
                    Domain::where('grant_id', $grant->id)->where('domain_id', $domain->domain_id)->delete();
                }
            }

            foreach ($grant->participationTypes as $participation) {
                if ( ! in_array($participation->participation_id, $pecParticipationIds)) {
                    ParticipationType::where('grant_id', $grant->id)->where('participation_id', $participation->participation_id)->delete();
                }
            }
        });

        // Sync Sage Data
        $this->event->syncSageData();
    }

    private function syncShopRanges(): void
    {
        $this->event->shopRanges()->delete();

        if (request()->filled('shop_range')) {
            $data = [];
            for ($i = 0; $i < count((array)request('shop_range.port')); ++$i) {
                $data[] = new EventShoppingRanges(['port' => request('shop_range.port.'.$i), 'order_up_to' => request('shop_range.order_up_to.'.$i)]);
            }
            $this->event->shopRanges()->saveMany($data);
        }
    }

    private function syncContactEligibility(Event $event, bool $pec_is_active)
    {
        if ( ! $event->pec->is_active && $pec_is_active) {
            // Reset Grant elibility from previously inactive PEC
            $this->pushMessages(
                (new GrantActions())->updateEligibleStatusForContacts($this->event),
            );
        } elseif ($event->pec->is_active && ! $pec_is_active) {
            // Reset all event contacts to not eligible
            $this->pushMessages(
                (new GrantActions())->setEligiblesToNull($this->event),
            );
        }
    }


    /**
     * Afficher les évènements passés
     */
    public function passed(): Renderable
    {
        return view('dashboard.passed_events');
    }

    private function syncEventServices()
    {
        # Manage event services
        collect(request('event_services', []))
            ->each(function ($service, $serviceId) {
                DB::table('event_service')->updateOrInsert(
                    [
                        'event_id'   => $this->event->id,
                        'service_id' => $serviceId,
                    ],
                    [
                        'max'                       => $service['max'] ?? 1,
                        'unlimited'                 => isset($service['unlimited']) && $service['unlimited'] ? 1 : null,
                        'service_date_doesnt_count' => isset($service['service_date_doesnt_count']) && $service['service_date_doesnt_count'] ? 1 : null,
                        'fo_family_position'        => $service['fo_family_position'] ?? 0,
                        'enabled'                   => (int)array_key_exists('service_id', $service),
                    ],
                );
            });

        $this->syncEventServiceSageValues();
    }

    private function syncEventServiceSageValues(): void
    {
        // Sync Sage Values
        $eventServicesSageValues = array_filter(request('sage.'.EventService::SAGEVAT));

        // d($eventServicesSageValues);
        foreach ($eventServicesSageValues as $key => $value) {
            $eventService = $this->event->eventServices->where('service_id', $key)->first();
            //    d($eventService);

            if ( ! $eventService) {
                continue;
            }
            /*
                        d([
                            'name'       => EventService::SAGEVAT,
                            'value'      => $value,
                            'model_id'   => $eventService->id,
                            'model_type' => EventService::class,
                        ]);
            */
            Sage::updateOrCreate(
                [
                    'model_id'   => $eventService->id,
                    'model_type' => EventService::class,
                    'name'       => EventService::SAGEVAT,
                ],
                [
                    'name'       => EventService::SAGEVAT,
                    'value'      => $value,
                    'model_id'   => $eventService->id,
                    'model_type' => EventService::class,
                ],
            );
        }
        //  exit;
    }

    public function restore(int $id): RedirectResponse
    {
        try {
            $sellable = Event::withTrashed()->findOrFail($id);
            $sellable->restore();
            $this->responseSuccess("L'évènement a été réactivé");

        } catch (Throwable) {

            $this->responseSuccess("Cet évènement n'existe pas");
        }

        return $this->sendResponse();
    }
}


