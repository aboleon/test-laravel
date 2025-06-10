<?php

namespace App\Http\Controllers\EventManager\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function index()
    {

        return view('accounting.index');
    }
}
