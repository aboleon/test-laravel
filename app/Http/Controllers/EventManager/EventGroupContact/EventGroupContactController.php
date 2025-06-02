<?php

namespace App\Http\Controllers\EventManager\EventGroupContact;

use App\Models\Event;
use App\Models\EventManager\EventGroup\EventGroupContact;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Actions\Suppressor;

class EventGroupContactController
{
    public function destroy(Event $event, EventGroupContact $eventGroupContact): RedirectResponse
    {
        $previousUrl = url()->previous();
        return (new Suppressor($eventGroupContact))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le contact est bien dissocié du groupe pour l\'événément.'))
            ->redirectTo($previousUrl)
            ->sendResponse();
    }
}