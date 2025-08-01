<?php

use App\Http\Controllers\{DashboardController, DownloadController, ForceDeleteController, GenericMediaController, NavController, GlobalExportController, RestoreController, SearchController};
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'verified', 'roles:' . (new User())->adminUsers()->pluck('id')->join('|')])
    ->prefix('panel')->name('panel.')->group(callback: function () {

        Route::get('dashboard', [DashboardController::class, 'show'])->name('dashboard');
        Route::get('generic_media', [GenericMediaController::class, 'show'])->name('generic_media');

        include('users.php');
        include('dictionnaries.php');
        include('project.php');

        // NAV
        Route::resource('nav', NavController::class);

        // Recherche
        Route::get('search', [SearchController::class, 'parse'])->name('search');

        // Generic
        Route::delete('forceDelete', [ForceDeleteController::class, 'process'])->name('forcedelete');
        Route::post('restore', [RestoreController::class, 'process'])->name('restore');

        Route::get('/download/file', [DownloadController::class, 'download'])
            ->name('download.file');

        Route::get('stream/merged-pdf/{token}', [GlobalExportController::class, 'streamMergedPdf'])
            ->name('stream.merged.pdf');


        Route::any('/', [DashboardController::class, 'show']);

    });



