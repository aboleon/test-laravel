<?php

namespace App\Http\Controllers\Front\Auth;

use App\Accessors\Accounts;
use App\Accessors\EventAccessor;
use App\Generators\Seo;
use App\Mail\Front\TemporaryPasswordMail;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MetaFramework\Traits\Responses;

class PasswordResetLinkController
{
    use Responses;

    public function create(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.forgot_password_title'), __("front/seo.forgot_password_description"));
        return view('front.auth.forgot-password', [
            'event' => $event,
            'registrationType' => request()->input('rtype', 'participant'),
        ]);
    }


    public function store(Request $request, string $locale, Event $event)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');



        $user = Accounts::getAccountByEmail($email);
        if (!$user) {
            return back()->withErrors(['email' => __('front/auth.email_not_linked_to_eligible_account')]);
        }


        $temporaryPassword = Str::random(10);
        $user->password = Hash::make($temporaryPassword);
        $user->save();
        $mail = new TemporaryPasswordMail(
            /**
             * for now, the user only connects using his main email
             * todo: make sure we don't want to allow the user to connect with any email address he might have
             */
            $user->email,
            $temporaryPassword,
            $event->texts->name,
            EventAccessor::getEventFrontUrl($event),
            EventAccessor::getBannerUrlByEvent($event)
        );

        $this->responseDebug($mail->render(), 'Mail envoyé');
        $this->flashResponse();

        $sent = Mail::to($user->email)->send($mail);

        return $sent ? redirect()
            ->route('front.event.login', $event)
            ->with('status', __('front/auth.forgot_password_success_message'))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => "An error occurred while sending the password reset email."]);
    }
}
