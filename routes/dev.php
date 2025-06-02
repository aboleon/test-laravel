<?php

use App\Http\Controllers\Testable\Availability;
use App\Models\User;

Route::middleware(['auth:sanctum', 'verified', 'roles:' . (new User())->devUsers()->pluck('id')->join('|')])
    ->prefix('dev')->name('dev.')->group(callback: function () {

        //Route::get('availability', [Availability::class, 'form']);
    });
