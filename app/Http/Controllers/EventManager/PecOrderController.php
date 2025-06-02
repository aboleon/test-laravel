<?php

namespace App\Http\Controllers\EventManager;

use App\DataTables\PecOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PecOrderController extends Controller
{
    public function index(Event $event): JsonResponse|View
    {
        $grantCount = $event->grants()->count();

        $dataTable = new PecOrderDataTable($event, $grantCount);


        return $dataTable->render('pec-orders.datatable.index', [
            'event' => $event,
            'grant_count' => $grantCount
        ]);
    }

}
