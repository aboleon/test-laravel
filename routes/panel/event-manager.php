<?php

use App\Http\Controllers\EventManager\{Accommodation\BlockedController,
    Accommodation\ContingentController,
    Accommodation\GrantController as AccommodationGrantController,
    Accommodation\GroupController,
    Accommodation\RoomController,
    AccommodationController,
    DepositController,
    EventContact\EventContactController,
    EventGroup\EventGroupController,
    EventGroupContact\EventGroupContactController,
    GrantController,
    GrantDepositController,
    InvoiceCancelController,
    PaymentController,
    PecOrderController,
    Program\ProgramContainerController,
    Program\ProgramInterventionController,
    Program\ProgramOrganizerController,
    Program\ProgramSessionController,
    SellableController,
    Transport\TransportController};
use App\Dashboards\OrdersDashboard;
use App\Http\Controllers\EventManagerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Order\AmendedAccommodationCartController;
use App\Http\Controllers\Order\AttributionController;
use App\Http\Controllers\Order\EventDepositController;
use App\Http\Controllers\Order\RefundController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('event-error/{event}', function (\App\Models\Event $event) {
    return view('errors.back-office-event', compact('event'));
})->name('event-error');

Route::prefix('manager')->name('manager.')->group(function () {
    Route::prefix('event/{event}')->name('event.')->group(function () {
        Route::get('/', [EventManagerController::class, 'show'])->name('show');

        //--------------------------------------------
        // accommodation
        //--------------------------------------------
        Route::resource('accommodation', AccommodationController::class)
            ->middleware('verify.accommodation.event')
            ->except(['create', 'store']);

        Route::prefix('accommodation/{accommodation}')
            ->name('accommodation.')
            ->middleware('verify.accommodation.event')
            ->group(function () {
                Route::prefix('rooms')->name('rooms.')->group(function () {
                    Route::get('edit', [RoomController::class, 'edit'])->name('edit');
                    Route::put('update', [RoomController::class, 'update'])->name('update');
                    Route::get('contingent', [ContingentController::class, 'edit'])->name('contingent');
                    Route::put('contingent', [ContingentController::class, 'update'])->name('contingent.update');
                    Route::get('blocked', [BlockedController::class, 'edit'])->name('blocked');
                    Route::put('blocked', [BlockedController::class, 'update'])->name('blocked.update');
                    Route::get('grant', [AccommodationGrantController::class, 'edit'])->name('grant');
                    Route::put('grant', [AccommodationGrantController::class, 'update'])->name('grant.update');
                    Route::get('groups', [GroupController::class, 'edit'])->name('groups');
                });

                Route::prefix('roominglist')->name('roominglist.')->group(function () {
                    Route::get('export', [AccommodationController::class, 'exportRoomingList'])->name('export');
                    Route::get('report', [AccommodationController::class, 'reportRoomingList'])->name('report');
                });
            });


        //--------------------------------------------
        // sellable
        //--------------------------------------------
        Route::resource('sellable', SellableController::class);
        Route::get('sellable/recap/{sellable}/sales', [SellableController::class, 'salesRecapDatatableData'])->name('sellable.sales.recap_ajax');

        //--------------------------------------------
        // program
        //--------------------------------------------
        Route::prefix('program')->name('program.')->group(function () {
            Route::resource('organizer', ProgramOrganizerController::class);
            Route::get('containers', [ProgramContainerController::class, "index"])->name('containers.index');
            Route::put('containers/update', [ProgramContainerController::class, "update"])->name('containers.update');
            Route::resource('session', ProgramSessionController::class);
            Route::resource('intervention', ProgramInterventionController::class);
        });

        //--------------------------------------------
        // transport
        //--------------------------------------------
        Route::get('transport/undesired_data', [TransportController::class, 'undesiredData'])->name('transport.undesired_data');
        Route::get('transport/desired_data', [TransportController::class, 'desiredData'])->name('transport.desired_data');
        Route::resource('transport', TransportController::class)->except(['store', 'update']);
        Route::get('transport/{eventContact}/editByEventContact', [TransportController::class, 'editByEventContact'])->name('transport.editByEventContact');
        Route::put('transport/{eventContact}/updateByEventContact', [TransportController::class, 'updateByEventContact'])->name('transport.updateByEventContact');

        //--------------------------------------------
        // event contacts
        //--------------------------------------------
        Route::prefix('event_contact')->name('event_contact.')->group(function () {
            Route::get('ofgroup/{group}', [EventContactController::class, 'index'])->name('index');
            Route::get('ofgroup/{group}/with_order/{withOrder}', [EventContactController::class, 'indexWithOrder'])->name('index_with_order');
        });
        Route::resource('event_contact', EventContactController::class)->except(['index', 'create', 'store']);
        Route::get('event_contact/dashboard/transport/{eventContact}', [EventContactController::class, 'transportDatatableData'])->name('event_contact.dashboard.transport');
        Route::get('event_contact/dashboard/intervention/{eventContact}', [EventContactController::class, 'interventionDatatableData'])->name('event_contact.dashboard.intervention');
        Route::get('event_contact/dashboard/session/{eventContact}', [EventContactController::class, 'sessionDatatableData'])->name('event_contact.dashboard.session');
        Route::get('event_contact/dashboard/choosable/{eventContact}', [EventContactController::class, 'choosableDatatableData'])->name('event_contact.dashboard.choosable');
        Route::get('event_contact/pec/pec/{eventContact}', [EventContactController::class, 'pecDatatableData'])->name('event_contact.pec.pec');

        //--------------------------------------------
        // event groups
        //--------------------------------------------
        Route::resource('event_group', EventGroupController::class)->except(['create', 'store']);
        Route::get('event_group/dashboard/contact/{eventGroup}', [EventGroupController::class, 'eventGroupContactDatatableData'])->name('event_group.dashboard.contact');

        //--------------------------------------------
        // event group contacts
        //--------------------------------------------
        Route::delete('event_group_contact/{event_group_contact}', [EventGroupContactController::class, 'destroy'])->name('event_group_contact.destroy');

        //--------------------------------------------
        // payment
        //--------------------------------------------
        Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::delete('payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');

        //--------------------------------------------
        // invoice cancel (avoir)
        //--------------------------------------------
        Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::get('invoice_cancel', [InvoiceCancelController::class, 'index'])->name('invoice_cancel.index');
        Route::delete('invoice_cancel/{invoice_cancel}', [InvoiceCancelController::class, 'destroy'])->name('invoice_cancel.destroy');

        //--------------------------------------------
        //
        //--------------------------------------------
        Route::resource('grantdeposit', GrantDepositController::class);
        Route::resource('grants', GrantController::class);
        Route::get('deposit', [DepositController::class, 'index'])->name('deposit.index');
        Route::get('grants/recap/{grant}', [GrantController::class, 'recap'])->name('grants.recap');

        Route::get('grants/recap/{grant}/recap', [GrantController::class, 'grantRecapDatatableData'])->name('grants.recap_ajax');


        //--------------------------------------------
        // orders
        //--------------------------------------------
        Route::get('orders/orators', [OrderController::class, 'orators'])->name('orders.orators');
        Route::get('orders/dashboard', [OrdersDashboard::class, 'dashboard'])->name('orders.dashboard');
        Route::resource('orders', OrderController::class)
            ->middleware('verify.order.event')
            ->missing(function (\Illuminate\Http\Request $request) {
                $event = $request->route('event');
                return redirect()->route('panel.event-error', ['event' => $event])->with([
                    'event_error_message' => "Cette commande n'existe pas",
                ]);
            });

        Route::resource('orders.refunds', RefundController::class)->shallow()->except('show');
        Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::get('orders/{order}/attributions/{type}', [AttributionController::class, 'index'])
            ->name('orders.attributions')
            ->middleware('verify.order.event')
            ->missing(function (\Illuminate\Http\Request $request) {
                $event = $request->route('event');
                return redirect()->route('panel.event-error', ['event' => $event])->with([
                    'event_error_message' => "Cette commande n'existe pas",
                ]);
            });

        Route::prefix('events-deposits')->name('event_deposit.')->group(function () {
            Route::get('index', [EventDepositController::class, 'index'])->name('index');
            Route::get('export', [EventDepositController::class, 'export'])->name('export');
        });

        Route::get('pecorder/index', [PecOrderController::class, 'index'])->name('pecorder.index');

        Route::prefix('orders/{order}/accommodation/amend/{cart}')
            ->controller(AmendedAccommodationCartController::class)
            ->middleware('verify.order.event')
            ->group(function () {
                Route::get('/', 'amend')->name('orders.accommodation.amend');
                Route::post('/', 'store')->name('orders.accommodation.store-amended');
            });
    });
});
