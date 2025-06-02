<?php

namespace App\Listeners;

use App\Models\EventContact;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Log;
use Throwable;

class ClearCacheOnLogout
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     *
     * @return void
     */
    public function handle(Logout $event)
    {
        if (request()->routeIs('front.event.logout')) {

            session()->flush();
            try {
                $user = $event->user;
                $data = EventContact::query()->where('user_id', $user->id)->pluck('event_id', 'id');

                if ($data->isNotEmpty()) {
                    foreach ($data as $id => $event) {

                        // Clear cache for participation type
                        Cache::forget("participation_type_{$id}");

                        // Clear cache for main contact status
                        Cache::forget("is_main_contact_{$event}_{$user->id}");
                    }
                    //Log::info("Cleared cache for event contact id : {$id}, user_id {$user->id} and event_id: {$event}");
                }
            } catch (Throwable $e) {
                Log::error("Could not clear cache for event contact id : {$id}, user_id {$user->id} and event_id: {$event}");
                Log::error($e->getMessage());
            }
        }
    }
}
