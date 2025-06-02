<?php

namespace App\Providers;

use App\Events\AccountSaved;
use App\Events\ContactSaved;
use App\Listeners\{AccountCreated, AccountGlobalPecEligibility, AccountPecEligibility, ClearCacheOnLogout, LoginSuccessListener};
use Illuminate\Auth\Events\{Login, Logout, Registered};
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
           // SendEmailVerificationNotification::class,
            AccountCreated::class,
        ],

        Login::class => [
            LoginSuccessListener::class,
        ],
        Logout::class => [
            ClearCacheOnLogout::class,
        ],
        ContactSaved::class => [
            AccountPecEligibility::class
        ],
        AccountSaved::class => [
            AccountGlobalPecEligibility::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
