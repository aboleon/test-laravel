<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\EventContactToken;
use Auth;

class AutoConnectController extends EventBaseController
{
    private bool|string $token_auth_error = false;

    public function __construct()
    {

    }

    public function connectEventContactWithToken(string $token)
    {
        return $this->connectEventContactWithTokenToRoute($token, 'front.event.dashboard');
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function connectEventContactWithTokenToRoute(string $token, string $routeName)
    {
        // Process Token
        $token = EventContactToken::firstWhere('token', $token);

        $this->checkForErrors($token);
        if ($this->token_auth_error) {
            return redirect()->route('front.home', app()->getLocale())->with("genericError", $this->token_auth_error);
        }

        $token->validated_at = now();
        $token->save();

        Auth::logout();
        Auth::loginUsingId($token->eventContact->user_id);

        // Set Cache
        FrontCache::setEventContact($token->eventContact->event_id, $token->eventContact->user_id);
        FrontCache::setEvent($token->eventContact->event_id);


        return redirect()->route($routeName, [
            'locale' => app()->getLocale(),
            'event' => $token->eventContact->event_id,
        ]);


    }

    private function checkForErrors(?EventContactToken $token): void
    {
        if (!$token) {
            $this->token_auth_error = __("errors.event_contact_token");
            return;
        }

        if ($token->validated_at) {
            $this->token_auth_error = __("errors.event_contact_token_already_validated");
        }
    }
}
