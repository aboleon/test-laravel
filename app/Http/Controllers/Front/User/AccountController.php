<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Actions\Front\MyAccountAction;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Http\Requests\Front\User\AccountRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {

       Seo::generator(__('front/seo.account_title'));


       $domains = $event->domains->pluck('name','id')->sort()->toArray();

        return view('front.user.account')->with(['domains' => $domains]);

    }

    /**
     * @throws \Throwable
     */
    public function update(string $locale, Event $event)
    {
        $request   = new AccountRequest();
        $validator = Validator::make(request()->all(), $request->rules(), $request->messages());
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $account      = FrontCache::getAccount();
        $eventContact = FrontCache::getEventContact();

        return (new MyAccountAction($request))
            ->setAccount($account)
            ->setEvent($event)
            ->setRegistrationType($eventContact->participationType->group)
            ->setOptions([
                'allowParticipationTypeUpdate' => false,
            ])
            ->update();
    }
}
