<?php

namespace App\Services\Availability;

use App\Services\Availability\Interfaces\BlockedRoomRepository;
use App\Services\Availability\Interfaces\BookingRepository;
use App\Services\Availability\Interfaces\ContingentRepository;
use App\Services\Availability\Repositories\Eloquent\EloquentBlockedRoomRepository;
use App\Services\Availability\Repositories\Eloquent\EloquentBookingRepository;
use App\Services\Availability\Repositories\Eloquent\EloquentContingentRepository;
use Illuminate\Support\ServiceProvider;

class EventManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookingRepository::class, EloquentBookingRepository::class);
        $this->app->bind(ContingentRepository::class, EloquentContingentRepository::class);
        $this->app->bind(BlockedRoomRepository::class, EloquentBlockedRoomRepository::class);
    }
}
