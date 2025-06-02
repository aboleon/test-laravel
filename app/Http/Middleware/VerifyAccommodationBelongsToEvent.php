<?php

namespace App\Http\Middleware;

use App\Accessors\AccessControl;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccommodationBelongsToEvent
{

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route()->getName() === 'panel.manager.event.accommodation.index') {
            return $next($request);
        }

        $event = $request->route('event');
        $accommodation = $request->route('accommodation');

        $response = AccessControl::eventAccommodation($event,$accommodation);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        return $next($request);
    }
}
