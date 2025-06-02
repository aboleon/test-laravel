<?php

use App\Http\Controllers\{CustomPaymentController, Front\ContactController, Front\DivinePrivacyPolicyController, Front\EventController, Front\HomeController, Front\OrderAttributionController};
use App\Http\Controllers\Front\Auth\{AccountCreationController, AccountRegistrationController, AuthenticatedSessionController, PasswordResetLinkController};
use App\Http\Controllers\Front\Paybox\PayboxController;
use App\Http\Controllers\Front\User\{AccommodationController,
    AccountController,
    AutoConnectController,
    CartController,
    CoordinatesController,
    CredentialsController,
    DashboardController,
    DocumentsController,
    Group\GroupBuyController,
    Group\GroupCheckoutController,
    Group\GroupDashboardController,
    Group\GroupMembersController,
    Group\GroupOrdersController,
    InterventionController,
    InvitationController,
    LoginAsController,
    OrdersController,
    RemainingPaymentsController,
    SelectAccountTypeController,
    SendMainContactMailController,
    ServiceAndRegistrationController,
    SwitchParticipantController,
    TransportController};
use App\Http\Middleware\Front\{CheckParticipantConstraints, RedirectIfNotAuthenticated};
use App\Http\Middleware\Localization;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect(app()->getLocale());
});

Route::get('/dashboard', function () {
    return redirect(app()->getLocale()."/dashboard");
});

# CustomPayment Call
Route::prefix('payment')->name('custompayment.')->group(function () {
    Route::get('success', [CustomPaymentController::class, 'success'])->name('success');
    Route::get('cancel', [CustomPaymentController::class, 'cancel'])->name('cancel');
    Route::get('decline', [CustomPaymentController::class, 'decline'])->name('decline');
    Route::get('autoresponse', [CustomPaymentController::class, 'autoresponse'])->name('autoresponse');
    Route::post('sendPaymentMail/{model}', [CustomPaymentController::class, 'sendPaymentMail'])->name('payment_mail');
    Route::get('{uuid}', [CustomPaymentController::class, 'show'])->name('form');;
});

# PayBox
Route::get('/paybox/receiver/annule', [PayboxController::class, "cancelled"])->name('paybox.receiver.annule');
Route::get('/paybox/receiver/effectue', [PayboxController::class, "success"])->name('paybox.receiver.effectue');
Route::get('/paybox/receiver/refuse', [PayboxController::class, "rejected"])->name('paybox.receiver.refuse');
Route::get('/paybox/receiver/autoresponse', [PayboxController::class, "autoresponse"])->name('paybox.receiver.autoresponse');


Route::name('front.')->group(function () {
    Route::get('connect-by-token/{token}', [AutoConnectController::class, "connectEventContactWithToken"])->name('connect-by-token');


    //--------------------------------------------
    // localized routes
    //--------------------------------------------
    Route::prefix('{locale}')
        ->where(['locale' => implode('|', config('mfw.translatable.active_locales'))])
        ->middleware(Localization::class)
        ->group(function () {
            Route::get('create-account-by-token/{token}', [AccountRegistrationController::class, "confirmRegistrationDemand"])->name('confirm-public-account');
            Route::get('register-form/{token}', [AccountCreationController::class, 'create'])->name('register-public-account-form');
            Route::post('register-form/{token}', [AccountCreationController::class, 'store'])->name('post-public-account-form');
            //   Route::get('register-form/{token}', [AccountCreationController::class, 'create'])->name('register.form');

            Route::put('registerCredentials/{token}', [AccountCreationController::class, 'storeCredentials'])->name('registerCredentials');

            //--------------------------------------------
            // default routes
            //--------------------------------------------
            Route::get('/', [HomeController::class, 'home'])->name('home');


            //--------------------------------------------
            // event namespace
            //--------------------------------------------
            Route::prefix('event/{event}')->name('event.')->group(function () {
                //--------------------------------------------
                // only for connected users
                //--------------------------------------------
                Route::middleware([
                    RedirectIfNotAuthenticated::class,
                    CheckParticipantConstraints::class,
                ])->group(function () {
                    Route::prefix('group')->name('group.')->group(function () {
                        Route::get('dashboard', [GroupDashboardController::class, "index"])->name('dashboard');
                        Route::get('members', [GroupMembersController::class, "index"])->name('members');
                        Route::get('buy', [GroupBuyController::class, "index"])->name('buy');
                        Route::get('checkout', [GroupCheckoutController::class, "index"])->name('checkout');
                        Route::get('orders', [GroupOrdersController::class, "index"])->name('orders');
                        Route::get('orders/{order}', [GroupOrdersController::class, "edit"])->name('orders.edit');
                        Route::get('attributions', [OrderAttributionController::class, "index"])->name('attributions.index');
                        Route::get('attributions/{type}', [OrderAttributionController::class, "edit"])->name('attributions.edit');
                    });

                    Route::get('switch-to-group-member/{group}/{user}/{routeType}', [SwitchParticipantController::class, "connectAsGroupMember"])->name('switch-to-group-member');
                    Route::get('switch-back-and-go-to-group-members', [SwitchParticipantController::class, "disconnectAndBackToGroupMembers"])->name('switch-back-and-go-to-group-members');
                    Route::get('switch-back-and-go-to-group-cart', [SwitchParticipantController::class, "disconnectAndBackToGroupCart"])->name('switch-back-and-go-to-group-cart');
                    Route::get('switch-back-and-go-to-group-buy', [SwitchParticipantController::class, "disconnectAndBackToGroupBuy"])->name('switch-back-and-go-to-group-buy');

                    Route::get('select-account-type', [SelectAccountTypeController::class, "index"])->name('select-account-type');
                    Route::get('login-as-user', [LoginAsController::class, "loginAsUser"])->name('login-as-user');
                    Route::get('login-as-group-manager', [LoginAsController::class, "loginAsGroupManager"])->name('login-as-group-manager');

                    Route::get('dashboard', [DashboardController::class, "index"])->name('dashboard');
                    Route::get('group-management-demand/{contact}', [DashboardController::class, "groupManagementDemand"])->name('group-management-demand');
                    Route::get('remaining-payments', [RemainingPaymentsController::class, "index"])->name('remaining-payments');
                    Route::get('orders', [OrdersController::class, "index"])->name('orders.index');
                    Route::get('orders/{order}', [OrdersController::class, "edit"])->name('orders.edit');

                    Route::prefix('amend/accommodation')
                        ->name('amend.accommodation.')
                        ->group(function () {
                            Route::get('cart/{cart}', [AccommodationController::class, "amendCart"])->name('cart');
                            Route::get('order/{order}', [AccommodationController::class, "amendOrder"])->name('order');
                        });

                    Route::get('account', [AccountController::class, "edit"])->name('account.edit');
                    Route::put('account', [AccountController::class, "update"])->name('account.update');
                    Route::get('credentials', [CredentialsController::class, "edit"])->name('credentials.edit');
                    Route::put('credentials', [CredentialsController::class, "update"])->name('credentials.update');
                    Route::get('documents', [DocumentsController::class, "edit"])->name('documents.edit');
                    Route::get('coordinates', [CoordinatesController::class, "edit"])->name('coordinates.edit');
                    Route::get('service-registration', [ServiceAndRegistrationController::class, "dashboard"])->name('service_and_registration.edit');
                    Route::get('accommodation', [AccommodationController::class, "edit"])->name('accommodation.edit');
                    Route::get('intervention', [InterventionController::class, "edit"])->name('intervention.edit');
                    Route::get('invitation', [InvitationController::class, "edit"])->name('invitation.edit');

                    Route::get('cart', [CartController::class, "edit"])->name('cart.edit');

                    Route::get('transport', [TransportController::class, "edit"])->name('transport.edit');
                    Route::put('transport', [TransportController::class, "update"])->name('transport.update');
                    Route::put('transport-update-participant-step-departure', [TransportController::class, "updateParticipantStepDeparture"])->name('transport.update.participant.step.departure');
                    Route::put('transport-update-participant-step-return', [TransportController::class, "updateParticipantStepReturn"])->name('transport.update.participant.step.return');
                    Route::post('transport-update-participant-step-documents', [TransportController::class, "updateParticipantStepDocuments"])->name('transport.update.participant.step.documents');
                    Route::put('transport-update-participant-step-transfer', [TransportController::class, "updateParticipantStepTransfer"])->name('transport.update.participant.step.transfer');
                    Route::put('transport-update-participant-step-recap', [TransportController::class, "updateParticipantStepRecap"])->name('transport.update.participant.step.recap');
                    Route::put('transport-update-divine-step-info', [TransportController::class, "updateDivineStepInfo"])->name('transport.update.divine.step.info');
                    Route::put('transport-update-divine-step-departure', [TransportController::class, "updateDivineStepDeparture"])->name('transport.update.divine.step.departure');
                    Route::put('transport-update-divine-step-return', [TransportController::class, "updateDivineStepReturn"])->name('transport.update.divine.step.return');
                    Route::put('transport-update-divine-step-transfer', [TransportController::class, "updateDivineStepTransfer"])->name('transport.update.divine.step.transfer');
                    Route::put('transport-update-divine-step-recap', [TransportController::class, "updateDivineStepRecap"])->name('transport.update.divine.step.recap');
                    // tmp routes
                    Route::get('transport-none', [TransportController::class, "edit"])->name('transport.editNone');
                    Route::get('transport-participant', [TransportController::class, "edit"])->name('transport.editParticipant');
                    Route::get('transport-divine', [TransportController::class, "edit"])->name('transport.editDivine');

                    //
                    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
                });

                Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
                Route::post('login', [AuthenticatedSessionController::class, 'loginFrontUser']);

                Route::get('register', [AccountRegistrationController::class, 'create'])->name('register');
                Route::put('register-participation-type', [AccountRegistrationController::class, 'storeParticipationType'])->name('registerParticipationType');

                Route::post('send-main-contact-mail', [SendMainContactMailController::class, 'sendMainContactMail'])->name('sendMainContactMail');

                Route::put('register-login', [AccountRegistrationController::class, 'loginRegisteringUserAndRedirectToDashboard'])->name('registerLogin');
                Route::put('associate-group-member-and-back', [AccountRegistrationController::class, 'associateGroupMemberAndBackToGroupMembers'])->name('associate-group-member-and-back');
                Route::post('register-by-email', [AccountRegistrationController::class, 'storeRegistrationDemand'])->name('registerByEmail');
                Route::get('email-already-registered', [AccountRegistrationController::class, 'showEmailAlreadyRegistered'])->name('register.emailAlreadyRegistered');
                Route::get('register-email-sent', [AccountRegistrationController::class, 'showRegisterEmailSent'])->name('register.emailSent');
                Route::get('register-error', [AccountRegistrationController::class, 'showError'])->name('register.error');
                Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
                Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');


                Route::get('contact', [ContactController::class, "show"])->name('contact');
                Route::get('/privacy-policy', [DivinePrivacyPolicyController::class, 'show'])->name('privacy-policy');

                Route::get('/{slug}', [EventController::class, 'event'])->name('show');
            });
        });
});



