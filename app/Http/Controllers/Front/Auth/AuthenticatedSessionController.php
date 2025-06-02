<?php

namespace App\Http\Controllers\Front\Auth;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Accessors\Front\FrontCache;
use App\Enum\RegistrationType;
use App\Generators\Seo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Authentication\LoginRequest;
use App\Models\Account;
use App\Models\Event;
use App\Models\UserRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MetaFramework\Traits\Responses;

class AuthenticatedSessionController extends Controller
{
    use Responses;

    private string $defaultRegistrationType;

    public function __construct()
    {
        $this->defaultRegistrationType = RegistrationType::LOGIN->value;
    }

    public function create(string $locale, Event $event)
    {
        if (auth()->check()) {
            auth()->logout();
        }

        $registrationType = request()->input('rtype', $this->defaultRegistrationType);

        Seo::generator(__('front/seo.login_title'), __("front/seo.login_description"));

        /*
         * A voir, debug sur invalidation session livewire
        session()->invalidate();
        session()->regenerate(true);
        */
        return view('front.auth.login', [
            'registrationType' => $registrationType,
            'event'            => $event,
        ]);
    }

    public function loginFrontUser(LoginRequest $request, string $locale, Event $event): RedirectResponse
    {
        # Login
        $request->authenticate();

        # Session
        FrontCache::setEvent($event);

        # Account
        $account = Account::find(auth()->id());

        $registrationType = request()->input('registration_type', $this->defaultRegistrationType);
        request()->merge(['registration_type' => $registrationType]);

        $redirectUrl = route('front.event.dashboard', $event);


        # Event Contact
        $ec = EventContactAccessor::getEventContactByEventAndUser($event->id, $account->id);

        if ($ec) {
            # Session
            FrontCache::setEventContactFromInstance($ec);

            if (FrontCache::isEventMainContact()) {
                $redirectUrl = route(
                    match ($registrationType) {
                        RegistrationType::GROUP->value => 'front.event.group.dashboard',
                        RegistrationType::PARTICIPANT->value => 'front.event.dashboard',
                        default => 'front.event.select-account-type',
                    },
                    ['event' => $event],
                );
            } elseif ($registrationType == RegistrationType::GROUP->value) {
                $redirectUrl = route('front.event.group-management-demand', [
                    'locale'  => $locale,
                    'event'   => $event,
                    'contact' => $ec,
                ]);
            }

            session()->forget('registration_email');

            if ($registrationType == RegistrationType::GROUP->value) {
                FrontCache::setGroupManager();
            }
        } else {
            # User with existing account but not linked to event, coming from unspecified login
            if ($registrationType == RegistrationType::LOGIN->value) {
                $this->responseWarning(__('front/register.you_have_account_but_not_for_this_event'));
                $this->redirect_to = EventAccessor::getEventFrontUrl($event);

                return $this->sendResponse();
            }

            if ($registrationType == RegistrationType::SPEAKER->value) {
                return back()->withErrors(['speakerLogin' => true]);
            }

            # Create UserRegistration Instance
            $registrationInstance = UserRegistration::query()->firstOrCreate(
                [
                    'email'    => $account->email,
                    'event_id' => $event->id,
                ],
                [
                    'registration_type' => $registrationType,
                    'event_id'          => $event->id,
                    'validated_at'      => now(),
                    'account_id'        => $account->id,
                    'options'           => [
                        'subscribe_newsletter' => 0,
                        'subscribe_sms'        => 0,
                    ],
                ],
            );

            return redirect()->route('front.register-public-account-form', [
                'locale' => app()->getLocale(),
                'token'  => $registrationInstance->id,
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Log::info("Logout with destroy method from AuthenticatedSessionController");

        $preservedEntry = $request->session()->get('session_response');


        // Perform logout
        Auth::guard('web')->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($preservedEntry) {
            $request->session()->put('session_response', $preservedEntry);
        }

        // Redirect to a default route or a route passed in the request
        return redirect(request('redirect_to') ?: '/');
    }
}
