<?php

namespace App\Http\Controllers\EventManager;

use App\DataTables\InvoiceCancelDataTable;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\InvoiceCancel\InvoiceCancel;
use MetaFramework\Actions\Suppressor;

class InvoiceCancelController extends Controller
{
    public function index(Event $event)
    {
        $dataTable = new InvoiceCancelDataTable($event);

        return $dataTable->render('events.manager.invoice_cancel.datatable.index', ['event' => $event]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, InvoiceCancel $invoiceCancel)
    {
        return (new Suppressor($invoiceCancel))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('L\'avoir est supprimé.'))
            ->redirectTo(route('panel.manager.event.invoice_cancel.index', $event))
            ->sendResponse();
    }
}
