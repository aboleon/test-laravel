<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class AuthenticateResetUser
{

    public function handle(PasswordReset $event)
    {
        return redirect()->to('https://google.com');
    }

}
