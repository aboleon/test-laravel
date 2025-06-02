<?php

namespace App\Http\Controllers\Order;

use App\DataTables\EventDepositDataTable;
use App\Enum\EventDepositStatus;
use App\Exports\EventDepositsExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use MetaFramework\Traits\Responses;

class EventDepositController extends Controller
{
    use Responses;


    /**
     * Display a listing of the resource.
     */
    public function index(Event $event): JsonResponse|View
    {
        $status = request()->filled('status') && in_array(request('status'), EventDepositStatus::keys(), true) ? request('status') : null;

        $dataTable = new EventDepositDataTable($event, $status);
        return $dataTable->render('event-deposits.datatable.index', ['event' => $event]);
    }

    public function export(Event $event)
    {
        $filename = 'event_deposits_event_' . $event->id . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new EventDepositsExport($event), $filename);
    }
}
