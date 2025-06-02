<?php

namespace App\Http\Controllers\Front\Auth;

use App\Accessors\Accounts;
use App\Actions\Front\MyAccountAction;
use App\Generators\Seo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\User\AccountRequest;
use App\Http\Requests\Front\User\CredentialsRequest;
use App\Models\Account;
use App\Models\UserRegistration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use MetaFramework\Services\Passwords\PasswordBroker;
use MetaFramework\Traits\Responses;
use Throwable;

class AccountCreationController extends Controller
{
    use Responses;


    public function create(string $locale, string $token)
    {
        $stepNumber = request('step', 1);
        $maxStep    = session("registering_user_max_step_number", 1);
        if ($stepNumber > $maxStep) {
            $maxStep = $stepNumber;
            session(["registering_user_max_step_number" => $maxStep]);
        }

        $this->setSeoData();

        try {
            $instance = UserRegistration::findOrFail($token);

            if ($instance->terminated_at && $stepNumber < $maxStep) {
                $this->responseWarning(__('front/register.you_have_already_registered'));
            }
        } catch (ModelNotFoundException) {
            $this->responseError(__('front/register.token_not_found'));
        }

        if ($this->hasErrors()) {
            $this->flashResponse();

            return view('errors.front');
        }
        $account = $instance?->account ?: (Account::firstWhere('email',$instance->email) ?: (new Account()));


        if ( ! $account?->id) {
            $account->email = $instance->email;
        } else {
            $instance->account_id = $account->id;
            $instance->save();
        }

        return view('front.auth.register-form', [
            'instance'             => $instance,
            'registrationType'     => $instance->registration_type ?: 'to_set',
            'event'                => $instance->event,
            'stepNumber'           => $stepNumber,
            'maxStepNumber'        => $maxStep,
            'account'              => $account,
            'event_group_id'       => request('event_group_id'),
        ]);
    }

    public function storeCredentials(CredentialsRequest $request, string $locale, UserRegistration $token)
    {

        try {
            $password_broker = (new PasswordBroker($request));
            $token->account()->update([
                'password' => $password_broker->getEncryptedPassword(),
            ]);
        } catch (Throwable $e) {
            Log::error('Transaction failed: '.$e->getMessage());
            $this->responseError(__("errors.cant_create_password"));
        }


        if ($this->hasErrors()) {
            $this->fetchResponse();
            return $this->sendResponse();
        }

        $token->terminated_at = now();
        $token->save();

        session()->forget('registration_temp_password');

        // dispatch Event Account Created

        return redirect()->route('front.register-public-account-form', [
            'locale'         => app()->getLocale(),
            'token'          => $token->id,
            'event_group_id' => request("event_group_id"),
            'step'           => 5,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(AccountRequest $request, string $locale, UserRegistration $token)
    {
        $event_group_id = request('event_group_id', false);
        $account        = Accounts::getAccountByEmail($token->email) ?: new Account();

        return (new MyAccountAction($request))
            ->setAccount($token?->account ?: new Account())
            ->setEvent($token->event)
            ->setRegistrationInstance($token)
            ->setRegistrationType($token->registration_type)
            ->setOptions([
                'createEventContact'   => true,
                'subscribe_newsletter' => $this->options['subscribe_newsletter'] ?? 0,
                'subscribe_sms'        => $this->options['subscribe_sms'] ?? 0,
                'existing_account'     => (bool)$account?->id,
                'event_group_id'       => $event_group_id,
            ])
            ->isRegistering()
            ->register();
    }


    private function setSeoData()
    {
        Seo::generator(__('front/seo.register_title'), __('front/seo.register_description'));
    }
}
