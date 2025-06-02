<?php

namespace App\Http\Controllers;

use App\Models\EventContactToken;
use Illuminate\Http\Request;

class EventContactTokenController extends Controller
{
    public function index()
    {
        return EventContactToken::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_contact_id' => ['required', 'exists:events_contacts'],
            'token'            => ['required'],
            'generated_at'     => ['required', 'date'],
            'validated_at'     => ['required', 'date'],
        ]);

        return EventContactToken::create($data);
    }

    public function show(EventContactToken $eventContactToken)
    {
        return $eventContactToken;
    }

    public function update(Request $request, EventContactToken $eventContactToken)
    {
        $data = $request->validate([
            'event_contact_id' => ['required', 'exists:events_contacts'],
            'token'            => ['required'],
            'generated_at'     => ['required', 'date'],
            'validated_at'     => ['required', 'date'],
        ]);

        $eventContactToken->update($data);

        return $eventContactToken;
    }

    public function destroy(EventContactToken $eventContactToken)
    {
        $eventContactToken->delete();

        return response()->json();
    }
}
