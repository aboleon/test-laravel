<?php

namespace App\Http\Controllers\EventManager;

use App\DataTables\OrderPaymentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use MetaFramework\Actions\Suppressor;

class PaymentController extends Controller
{
    public function index(Event $event)
    {
        $dataTable = new OrderPaymentDataTable($event);

        return $dataTable->render('events.manager.payment.datatable.index', ['event' => $event]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Payment $payment)
    {
        return (new Suppressor($payment))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le paiement est supprimé.'))
            ->redirectTo(route('panel.manager.event.payment.index', $event))
            ->sendResponse();
    }
}
