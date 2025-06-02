<?php

namespace App\Http\Controllers;

use App\Accessors\Accounts;
use App\Accessors\Establishments;
use App\Accessors\ParticipationTypes;
use App\Actions\Account\EventClientActions;
use App\Actions\Account\EventContactActions;
use App\Actions\Account\Replicator;
use App\Actions\AccountProfile\Profile as AccountProfileManager;
use App\Actions\EventManager\GrantActions;
use App\Actions\EventManager\Program\AssociateEventContactToInterventionAction;
use App\Actions\EventManager\Program\AssociateEventContactToSessionAction;
use App\Actions\GroupContactActions;
use App\DataTables\AccountDataTable;
use App\Enum\OrderClientType;
use App\Enum\SavedSearches;
use App\Enum\UserType;
use App\Events\AccountSaved;
use App\Http\Requests\AccountRequest;
use App\Models\{Account, AccountPhone, AdvancedSearchFilter, Event, EventContact, EventManager\Program\EventProgramIntervention, EventManager\Program\EventProgramSession, Group};
use App\Printers\EventPrinter;
use App\Traits\{Locale, Models\AccountModelTrait, Models\EventModelTrait, SelectableValues};
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Passwords\PasswordBroker;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Responses;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class AccountController extends Controller
{
    use Locale;
    use SelectableValues;
    use Responses;
    use ValidationTrait;
    use EventModelTrait;
    use AccountModelTrait;

    private int $participation_type_id;


    public function __construct()
    {
        $this->participation_type_id = ParticipationTypes::defaultId();
    }


    public function index(AccountDataTable $dataTable, string $role): JsonResponse|View
    {
        return $dataTable->render('accounts.index', [
            'searchFilters' => AdvancedSearchFilter::getFilters(SavedSearches::CONTACTS->value),
            'role'     => $role,
            'archived' => request()->routeIs('panel.accounts.archived'),
        ]);
    }

    public function create(): Renderable
    {
        $account = new Account();

        // Associer un groupe
        $group = request()->filled('group')
            ? Group::find(request('group'))
            : new Group();

        $group_msg = $group->id
            ? "Ce compte sera affecté comme contact du groupe <a href='".route('panel.groups.edit', $group)."'>".$group->name.' / '.$group->company.'</a>'
            : null;

        // Associer un évènement
        $event = request()->filled('event')
            ? Event::find(request('event'))
            : new Event();

        $associateToEvent = (string)request('callback') == 'associateToEvent';
        $as_type          = (string)request('associate_type');
        $associate_type   = in_array($as_type, ['client', OrderClientType::CONTACT->value]) ? $as_type : 'client';

        if ($associateToEvent) {
            $this->responseNotice("Ce compte sera associé comme <b>".($associate_type)."</b> à l'évènement <a href='".route('panel.events.edit', $event)."'><b>".(new EventPrinter($event))->names().'</b></a>');
            $this->flashResponse();
        }

        return view('accounts.edit')->with(
            array_merge(
                $this->sharedEditableData($account),
                [
                    'photo_media_settings'  => $account->photoMediaSettings(),
                    'route'                 => route('panel.accounts.store'),
                    'group_msg'             => $group_msg,
                    'associate_group'       => $group->id,
                    'participation_type_id' => (int)request('participation_type_id'),
                    'associate_event'       => $associateToEvent ? $event->id : null,
                    'associate_type'        => $associate_type,
                    'activity_histories'    => collect(),
                ],
            ),
        );
    }

    public function edit(int $account_id): Renderable
    {
        $account = Account::withTrashed()->findOrFail($account_id);

        return view('accounts.edit')->with(
            $this->getAccountEditViewData($account),
        );
    }

    public function getAccountEditViewData(Account $account): array
    {
        $activity_histories = Activity::where('subject_type', Account::class)
            ->where('log_name', 'contact-history')
            ->where('subject_id', $account->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return array_merge(
            $this->sharedEditableData($account),
            [
                'activity_histories'   => $activity_histories,
                'photo_media_settings' => $account->photoMediaSettings(),
                'route'                => route('panel.accounts.update', $account),
            ],
        );
    }

    /**
     * @throws Throwable
     */
    public function store(): RedirectResponse
    {
        $this->tabRedirect();

        $validation                = new AccountRequest();
        $this->validation_rules    = $validation->rules();
        $this->validation_messages = $validation->messages();

        $this->validation();

        $password_broker = (new PasswordBroker(request()))->passwordBroker();

        $this->validated_data['user']['password'] = $password_broker->getEncryptedPassword();
        $this->validated_data['user']['type']     = UserType::ACCOUNT->value;

        DB::beginTransaction();

        //$this->responseNotice($password_broker->printPublicPassword());
        try {
            $account = Account::create($this->validated_data['user']);

            $this->setAccount($account);

            $this->manageBlacklisted();
            (new AccountProfileManager($account, $this->validated_data['profile']))->create();


            # Create phone
            if ( ! empty($this->validated_data['phone']['phone']) && ! empty($this->validated_data['phone']['country_code'])) {
                $account->phones()->save(new AccountPhone(array_merge($this->validated_data['phone'], ['default' => 1])));
            }

            // event(new Registered($account));

            /*
             *
             * $this->responseSuccess(__('auth.verification_link_sent_admin'));
            */

            $this->responseSuccess("Le compte a été créé.");
            $this->redirect_to = route('panel.accounts.edit', ['account' => $account, 'role' => request('profile.account_type')]);
            $this->saveAndRedirect(route('panel.accounts.index', request('profile.account_type')));

            event(new AccountSaved($account));

            if (request()->filled('intervention_id')) {
                $intervention = EventProgramIntervention::find(request('intervention_id'));
                if ($intervention) {
                    $event    = $intervention->session->programDay->event;
                    $ecAction = (new EventContactActions())
                        ->enableAjaxMode()
                        ->setAccount($account->id)
                        ->setEvent($event->id)
                        ->associate();
                    $this->pushMessages($ecAction);

                    if ($ecAction->getEventContact()) {
                        $this->pushMessages(
                            (new AssociateEventContactToInterventionAction(
                                eventContact: $ecAction->getEventContact(),
                                intervention: $intervention,
                            ))->associate(),
                        );
                    } else {
                        $this->responseWarning("L'association du contact à l'intervention a échoué car aucun EventContact n'a été trouvé.");
                    }

                    $this->redirectTo(route('panel.manager.event.program.intervention.edit', [
                        'event'        => $event,
                        'intervention' => $intervention->id,
                    ]));
                }
            } elseif (request()->filled('session_id')) {
                $session = EventProgramSession::find(request('session_id'));
                if ($session) {
                    $event    = $session->programDay->event;
                    $ecAction = (new EventContactActions())
                        ->enableAjaxMode()
                        ->setAccount($account->id)
                        ->setEvent($event->id)
                        ->associate();

                    $this->pushMessages($ecAction);


                    if ($ecAction->getEventContact()) {
                        $this->pushMessages(
                            (new AssociateEventContactToSessionAction(
                                eventContact: $ecAction->getEventContact(),
                                session: $session,
                            ))->associate(),
                        );
                    } else {
                        $this->responseWarning("L'association du contact à la session a échoué car aucun EventContact n'a été trouvé.");
                    }

                    $this->redirectTo(route('panel.manager.event.program.session.edit', [
                        'event'   => $event,
                        'session' => $session->id,
                    ]));
                }
            } elseif (request()->filled('associate_group')) {
                $this->pushMessages(
                    (new GroupContactActions(account_id: $account->id, group_id: request('associate_group')))->associate(),
                );
                $this->redirectTo(route('panel.groups.edit', request('associate_group')));
            }

            if (request()->filled('associate_event')) {
                $this->setEvent(request('associate_event'));
                $this->throwException()->validateModelProperty('event', "L'évènement auquel doit être associé le contact n'est pas défini.");

                (string)(request('associate_type')) == OrderClientType::CONTACT->value
                    ? $this->associateAccountToEventAsContact()
                    : $this->associateAccountToEventAsClient();
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->responseException($e);
        }

        return $this->sendResponse();
    }


    public function update(Account $account): RedirectResponse
    {
        $this->tabRedirect();

        $validation                = new AccountRequest($account);
        $this->validation_rules    = $validation->rules();
        $this->validation_messages = $validation->messages();

        $this->validation();


        /**
         * Manage password change
         */
        $password_broker = (new PasswordBroker(request()));
        if ($password_broker->requestedChange()) {
            $this->validated_data['user']['password'] = $password_broker->getEncryptedPassword();
            $this->responseNotice($password_broker->printPublicPassword());
        }

        try {
            $account->update($this->validated_data['user']);

            $account->saveCustomFormFields();

            $this->manageBlacklisted();
            (new AccountProfileManager($account, $this->validated_data['profile']))->update();


            # Update phone

            if ( ! empty($this->validated_data['phone']['phone']) && ! empty($this->validated_data['phone']['country_code'])) {
                $defaultPhone = $account->phones->where('default', 1)->first();
                if ($defaultPhone) {
                    $defaultPhone->phone        = $this->validated_data['phone']['phone'];
                    $defaultPhone->country_code = $this->validated_data['phone']['country_code'];
                    $defaultPhone->save();
                } else {
                    $account->phones()->save(new AccountPhone(array_merge($this->validated_data['phone'], ['default' => 1])));
                }
            }

            event(new AccountSaved($account));

            $this->redirect_to = route('panel.accounts.edit', ['account' => $account, 'role' => request('profile.account_type')]);
            $this->saveAndRedirect(route('panel.accounts.index', request('profile.account_type')));
            $this->responseSuccess(__('ui.record_updated'));
        } catch (Throwable $e) {
            $this->responseWarning(__('ui.operation_failed'));
            $this->responseWarning($e->getMessage());
        }

        return $this->sendResponse();
    }

    /**
     * @throws Exception
     */
    public function destroy(Account $account): RedirectResponse
    {
        $response = (new Suppressor($account))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le compte est archivé.'))
            ->redirectTo(route('panel.accounts.index', $account->profile->account_type));

        activity('contact-history')
            ->on($account)
            ->by(Auth::user())
            ->withProperties(['account' => $account])
            ->log("Le compte de {$account->names()} a été archivé");

        return $response->sendResponse();
    }

    public function restore(int $account_id): RedirectResponse
    {
        try {
            $account = Account::withTrashed()->findOrFail($account_id);
            $account->restore();
            $this->responseSuccess("Le compte a été réactivé");
            activity('contact-history')
                ->on($account)
                ->by(Auth::user())
                ->withProperties(['account' => $account])
                ->log("Le compte de {$account->names()} a été réactivé");
        } catch (Throwable) {
            $this->responseSuccess("Ce compte n'existe pas");
        }

        return $this->sendResponse();
    }

    public function replicate(int $account_id): RedirectResponse
    {
        $account = Account::withTrashed()->findOrFail($account_id);

        try {
            $replicated = (new Replicator($account))();
            $this->responseSuccess("Le compte a été dupliqué");
            $this->redirectTo(route('panel.accounts.edit', $replicated));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param  \App\Models\Account  $account
     *
     * @return array<string, mixed>
     */
    private function sharedEditableData(Account $account): array
    {
        return [
            'account'               => $account,
            'accessor'              => (new Accounts($account)),
            'roles'                 => self::convertCollectionToValues($account->publicUsers(), 'label'),
            'role'                  => $account->profile?->account_type ?: request('role', 'all'),
            'participation_type_id' => request('participation_type_id'),
            'establishments'        => Establishments::orderedIdNameArray(),
            'intervention_id'       => request('intervention_id'),
            'session_id'            => request('session_id'),
        ];
    }

    private function manageBlacklisted(): void
    {
        $this->validated_data['profile']['blacklisted'] = Arr::has($this->validated_data['profile'], 'blacklisted') ? now() : null;
    }

    /**
     * @throws Exception
     */
    private function associateAccountToEventAsContact()
    {
        $participation_type_id = (int)request('participation_type_id');
        if (ParticipationTypes::isValidId($participation_type_id)) {
            $this->participation_type_id = $participation_type_id;
        }

        $action = (new EventContactActions())
            ->setAccount($this->account)
            ->setEvent($this->event)
            ->setParticipationTypeId($this->participation_type_id)
            ->associate();

        if ($action->hasErrors()) {
            $this->pushMessages($action);

            return;
        }

        $this->pushMessages($action);

        if ($action->getEventContact()) {
            $this->pushMessages(
                (new GrantActions())->updateEligibleStatusForSingleContact($action->getEvent(), $action->getEventContact()),
            );
        }

        $this->redirect_to = route('panel.manager.event.event_contact.edit', [
            'event'         => $action->getEvent()->id,
            'event_contact' => $action->getEventContact()->id,
        ]);
    }

    /**
     * @throws Exception
     */
    private function associateAccountToEventAsClient()
    {
        $action = (new EventClientActions($this->account, $this->event))
            ->associateToEvent();

        $this->pushMessages($action);

        if ( ! $action->hasErrors()) {
            $this->redirect_to = route('panel.events.edit', $this->event->id);
        }
    }
}

