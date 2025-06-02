<?php

namespace App\Http\Controllers\Front\Auth;

use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Accessors\Front\FrontCache;
use App\Actions\Front\Registration;
use App\Enum\RegistrationType;
use App\Enum\RegistrationType as RegistrationTypeAlias;
use App\Generators\Seo;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup\EventGroupContact;
use App\Models\SystemUser;
use App\Models\UserRegistration;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;
use MetaFramework\Traits\Responses;

class AccountRegistrationController
{
    use Responses;

    private int $subscribe_newsletter;
    private int $subscribe_sms;
    private string $registrationType;

    public function __construct()
    {
        $this->subscribe_newsletter = (int)request('subscribe_newsletter', 0);
        $this->subscribe_sms        = (int)request('subscribe_sms', 0);
        $this->registrationType     = (string)request()->input('rtype', RegistrationType::default());
    }


    public function create(string $locale, Event $event)
    {
        $this->setSeoData();

        if ((!in_array($this->registrationType, RegistrationTypeAlias::defaultGroups()))) {
            $this->responseError(__('front/errors.registration_type_not_allowed', ['type' => RegistrationType::translated($this->registrationType)]));
            $this->flashResponse();
            return view('errors.front');
        }

        return view('front.auth.register', [
            'registrationType' => $this->registrationType,
            'event'            => $event,
        ]);
    }


    public function storeRegistrationDemand(string $locale, Event $event)
    {
        $validator = Validator::make(['email' => request('email')], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $validator->validate()['email'];

        $account = Accounts::getAccountByEmail($email);

        if ($account) {
            $eventContact = EventContactAccessor::getEventContactByEventAndUser($event->id, $account->id);
            if ($eventContact) {
                $this->updateExistingAcount($eventContact);

                return redirect()->route('front.event.register.emailAlreadyRegistered', [
                    'locale' => $locale,
                    'event'  => $event,
                    'rtype'  => $this->registrationType,
                ]);
            }
        }

        if (SystemUser::where('email', $email)->exists()) {
            $this->responseWarning(__('front/register.this_email_is_not_allowed'));

            return $this->sendResponse();
        }

        $instance = $this->createRegistrationInstance($validator, $event);
        $this->pushMessages(
            (new Registration())->setEvent($event)->setInstance($instance)->sendMail(),
        );

        session()->flash('session_response', $this->fetchResponse());

        return view('front.auth.register-mail-sent', [
            'event'    => $event,
            'instance' => $instance,
        ]);
    }

    public function confirmRegistrationDemand($locale, string $token): Renderable
    {
        $this->setSeoData();

        try {
            $instance  = UserRegistration::findOrFail($token);
            $validated = (new Registration())->setInstance($instance)->validateByToken();

            if ($validated->hasErrors()) {
                $this->pushMessages($validated);
            }
        } catch (ModelNotFoundException) {
            $this->responseError(__('front/register.token_not_found'));
        }

        if ($this->hasErrors()) {
            $this->flashResponse();

            return view('errors.front');
        }

        $instance = $validated->getInstance();

        Seo::generator(__('front/seo.register_title'), __('front/seo.register_description'));

        return view('front.auth.register-mail-token-accepted', [
            'token'                => $instance->id,
            'registrationType'     => $instance->registration_type,
            'event'                => $instance->event,
            'email'                => $instance->email,
            'subscribe_newsletter' => $instance->options['subscribe_newsletter'] ?? 0,
            'subscribe_sms'        => $instance->options['subscribe_sms'] ?? 0,
        ]);
    }


    public function showEmailAlreadyRegistered(string $locale, Event $event)
    {
        $this->setSeoData();

        return view('front.auth.register-mail-found', [
            'registrationType' => $this->registrationType,
            'event'            => $event,
        ]);
    }

    public function showRegisterTokenExpired(string $locale, Event $event)
    {
        $this->setSeoData();

        return view('front.auth.register-mail-token-expired', [
            'registrationType' => $this->registrationType,
            'event'            => $event,
            'email'            => request()->input('email'),
        ]);
    }

    public function showError(string $locale, Event $event)
    {
        $this->setSeoData();

        return view('front.auth.register-error', [
            'registrationType' => $this->registrationType,
            'event'            => $event,
            'error'            => request()->input('error'),
        ]);
    }


    public function storeParticipationType(Request $request, string $locale, Event $event)
    {
        $validated = $request->validate([
            'participation_type' => 'required',
        ], [
            'participation_type.required' => "Le type de participation est obligatoire",
        ]);

        $user                                = Auth::user();
        $eventContact                        = FrontCache::getEventContact();
        $eventContact->participation_type_id = $validated['participation_type'];
        $eventContact->save();

        FrontCache::setEventContact($event->id, $user->id);

        return redirect()->back()->with('user.success', 'Type de participation modifié');
    }


    public function associateGroupMemberAndBackToGroupMembers(string $locale, Event $event): RedirectResponse
    {
        $eventGroupId = request('event_group_id');
        $email        = request('email');

        $account = Accounts::getAccountByEmail($email);

        if ( ! $email || ! $eventGroupId || ! $account) {
            return redirect()->back()->with('error', 'Erreur lors de l\'association du membre au groupe');
        }

        EventGroupContact::create([
            'event_group_id' => $eventGroupId,
            'user_id'        => $account->id,
        ]);

        return redirect()->route('front.event.group.members', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }

    public function loginRegisteringUserAndRedirectToDashboard(string $locale, Event $event)
    {
        $error   = null;
        $email   = request('email');
        $account = Accounts::getAccountByEmail($email);
        if ( ! $account) {
            $error = __("front/register.error_user_not_found");
        }

        if ($error) {
            return redirect()
                ->back()
                ->with('error', $error)
                ->withInput();
        }

        session()->forget(["registering_user_max_step_number", "registering_user_id"]);

        Auth::logout();
        Auth::login($account->user);

        FrontCache::setEventContact($event->id, $account->id);
        FrontCache::setEvent($event->id);

        return redirect()->route('front.event.dashboard', [
            'event' => $event,
        ]);
    }

    private function updateExistingAcount(EventContact $eventContact): void
    {
        if ($this->subscribe_newsletter || $this->subscribe_sms) {
            if ($this->subscribe_newsletter) {
                $eventContact->subscribe_newsletter = $this->subscribe_newsletter;
            }
            if ($this->subscribe_sms) {
                $eventContact->subscribe_sms = $this->subscribe_sms;
            }
            $eventContact->save();
        }

        $userRegistrationType = $eventContact->registration_type;
        if ($userRegistrationType) {
            $this->registrationType = $userRegistrationType;
        }
    }

    public function createRegistrationInstance(ValidatorInstance $validator, Event $event)
    {
        $email = $validator->validate()['email'];

        return UserRegistration::query()->firstOrCreate(
            [
                'email'    => $email,
                'event_id' => $event->id,
            ],
            [
                'registration_type' => $this->registrationType,
                'options'           => [
                    'subscribe_newsletter' => $this->subscribe_newsletter,
                    'subscribe_sms'        => $this->subscribe_sms,
                ],
            ],
        );
    }

    public function getRegistrationType()
    {

    }

    private function setSeoData()
    {
        Seo::generator(__('front/seo.register_title'), __('front/seo.register_description'));
    }

}
