<?php

namespace App\Http\Middleware\Front;

use App\Enum\UserType;
use Closure;
use Illuminate\Http\Request;
use URL;

class RedirectIfNotAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->type === UserType::ACCOUNT->value) {
            return $next($request);
        }

        return redirect()->route("front.home");
    }
}
