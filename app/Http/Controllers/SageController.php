<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;

class SageController extends Controller
{
    public function dashboard(Event $event): Renderable
    {

        return view('sage.dashboard_event')->with([
            'data' => 'hello',
            'event' => $event,
        ]);
    }
}
