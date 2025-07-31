<?php

use App\Http\Controllers\{EstablishmentController,
    EventController,
    HotelController,
    HotelHistoryController,
    MailController,
    ModalController,
    NewsletterListController,
    PlaceController,
    PlaceRoomController,
    SellableController};
use App\Http\Controllers\EventManager\Accounting\AccountingController;
use Illuminate\Support\Facades\Route;


# Routes 1er niveau BO
#---------------------

# Catalogue exposants
Route::get('sellables/archived', [SellableController::class, 'index'])->name('sellables.archived');
Route::post('restore/{id}', [SellableController::class, 'restore'])->name('sellables.restore');
Route::resource('sellables', SellableController::class);

# Events
Route::get('events/archived', [EventController::class, 'index'])->name('events.archived');
Route::post('events/restore/{id}', [EventController::class, 'restore'])->name('events.restore');
Route::resource('events', EventController::class)->except('show');
Route::get('events/passed', [EventController::class, 'passed'])->name('passed_events');
Route::resource('hotels', HotelController::class)->except('show');

# Hotels
Route::get('hotels/history/datatable', [HotelHistoryController::class, 'getDatatable'])->name('hotels.history.datatable');
Route::resource('establishments', EstablishmentController::class)->except('show');

# Lieux
Route::resource('places', PlaceController::class)->except('show');
Route::resource('places.rooms', PlaceRoomController::class)->shallow()->except('show');

# Comptabilité
Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('index');
    Route::get('/export/invoices/pdf', [AccountingController::class, 'exportInvoicesPdf'])->name('export.invoices.pdf');
    Route::get('/export/invoices/csv', [AccountingController::class, 'exportInvoicesCsv'])->name('export.invoices.csv');
    Route::get('/export/credits/pdf', [AccountingController::class, 'exportCreditsPdf'])->name('export.credits.pdf');
    Route::get('/export/credits/csv', [AccountingController::class, 'exportCreditsCsv'])->name('export.credits.csv');
});

# Dynamic modals
Route::get('modal/{requested}', [ModalController::class, 'distribute'])->name('modal');


Route::any('mail/{type}/{identifier}', [MailController::class, 'distribute'])->name('mailer');

# Routes 2d niveau BO Gestion évènement
#--------------------------------------
include('event-manager.php');
