<?php

namespace App\Http\Controllers;

use App\DataTables\HotelHistoryDataTable;
use App\DataTables\View\HotelHistoryView;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class HotelHistoryController extends Controller
{
    public function getDatatable(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $data = HotelHistoryView::where('hotel_id', request('hotel_id'));

        return Datatables::of($data)->make();
    }
}
