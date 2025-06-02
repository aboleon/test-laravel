<?php

namespace App\Http\Controllers\EventManager\Program;

use App\Accessors\Programs;
use App\Accessors\ProgramSessions;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class ProgramOrganizerController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $program = Programs::getOrganizerPrintViewCollection($event);

        return view('events.manager.program.organizer.index', [
            'event' => $event,
            'program' => $program,
            'format' => "print",
        ]);
    }

}
