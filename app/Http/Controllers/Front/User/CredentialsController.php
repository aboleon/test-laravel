<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Accounts;
use App\Accessors\Dates;
use App\Accessors\EventContactAccessor;
use App\Accessors\ParticipationTypes;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Http\Requests\Front\User\AccountRequest;
use App\Http\Requests\Front\User\CredentialsRequest;
use App\Models\AccountPhone;
use App\Models\Event;
use App\Models\ParticipationType;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use MetaFramework\Mediaclass\Controllers\FileUploadImages;
use MetaFramework\Mediaclass\Models\Media;
use MetaFramework\Services\Passwords\PasswordBroker;
use Throwable;

class CredentialsController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {


        Seo::generator(__('front/seo.credentials_title'));

        $user = Auth::getUser();
        return view('front.user.credentials', [
            "user" => $user,
            "event" => $event,
        ]);
    }

    public function update(CredentialsRequest $request, string $locale, Event $event)
    {
        $user = Auth::getUser();
        $account = $user->account;

        try {
            $password_broker = (new PasswordBroker($request));
            $account->update([
                'password' => $password_broker->getEncryptedPassword(),
            ]);


        } catch (Throwable $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            throw $e;
        }


        return redirect()->route('front.event.credentials.update', $event)
            ->with('success', __('front/account.update_success'));
    }
}
