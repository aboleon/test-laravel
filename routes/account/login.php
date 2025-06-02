<?php

use App\Http\Controllers\Account\LoginController;

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('auth', [LoginController::class, 'auth'])->name('auth');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
