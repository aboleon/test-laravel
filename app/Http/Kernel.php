<?php

namespace App\Http;

use App\Http\Middleware\{Authenticate, EncryptCookies, PreventRequestsDuringMaintenance, RedirectIfAuthenticated, Roles, TrimStrings, TrustProxies, VerifyAccommodationBelongsToEvent, VerifyCsrfToken, VerifyOrderBelongsToEvent};
use Illuminate\Auth\{Middleware\AuthenticateWithBasicAuth, Middleware\Authorize, Middleware\EnsureEmailIsVerified, Middleware\RequirePassword};
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\{Middleware\HandleCors, Middleware\SetCacheHeaders};
use Illuminate\Routing\{Middleware\SubstituteBindings, Middleware\ThrottleRequests, Middleware\ValidateSignature};
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware
        = [
            // \App\Http\Middleware\TrustHosts::class,
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups
        = [
            'web' => [
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ],

            'api' => [
                // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                'throttle:api',
                SubstituteBindings::class,
            ],
        ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware
        = [
            'auth'                       => Authenticate::class,
            'auth.basic'                 => AuthenticateWithBasicAuth::class,
            'auth.session'               => AuthenticateSession::class,
            'cache.headers'              => SetCacheHeaders::class,
            'can'                        => Authorize::class,
            'guest'                      => RedirectIfAuthenticated::class,
            'password.confirm'           => RequirePassword::class,
            'roles'                      => Roles::class,
            'signed'                     => ValidateSignature::class,
            'throttle'                   => ThrottleRequests::class,
            'verified'                   => EnsureEmailIsVerified::class,
            'verify.accommodation.event' => VerifyAccommodationBelongsToEvent::class,
            'verify.order.event'         => VerifyOrderBelongsToEvent::class,
        ];
}
