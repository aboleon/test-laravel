<?php

namespace App\Http\Controllers\EventManager\Accommodation;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Accommodation\Room;
use App\Models\EventManager\Accommodation\RoomGroup;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Traits\Responses;

class RoomController extends Controller
{
    use Responses;

    public function edit(Event $event, Accommodation $accommodation): Renderable
    {
        return view('events.manager.accommodation.rooms')->with([
            'event' => $event,
            'accommodation' => $accommodation,
        ]);
    }

    public function update(Event $event, Accommodation $accommodation): RedirectResponse
    {
        if (!request()->has('room_group_key')) {
            $this->responseWarning("Aucune information à traiter");
            return $this->sendResponse();
        }

        try {
            foreach (request('room_group_key') as $key) {

                $accessor = is_numeric($key) ? 'room_group_' . $key : $key;
                $data = [
                    'event_accommodation_id' => $accommodation->id,
                    'name' => request($accessor . '.name'),
                    'description' => request($accessor . '.description'),
                ];

                if(is_numeric($key)) {
                    $group = RoomGroup::find($key);
                    $group->update($data);
                } else {
                    $group = RoomGroup::create($data);
                }

                if (request()->has($accessor . '.type')) {
                    for ($i = 0; $i < count(request($accessor . '.type')); ++$i) {
                        if (empty(request($accessor . '.type.' . $i))) {
                            continue;
                        }
                        $data_room = [
                            'room_id' => request($accessor . '.type.' . $i),
                            'capacity' => request($accessor . '.capacity.' . $i),
                        ];

                        is_numeric(request($accessor . '.room_id.' . $i))
                            ? Room::where('id', request($accessor . '.room_id.' . $i))->update($data_room)
                            : $group->rooms()->save(new Room($data_room));
                    }
                }
            }
            $this->responseSuccess(__('ui.record_created'));
            $this->saveAndRedirect(route('panel.manager.event.show', $event));

        } catch (\Throwable $e) {
            //$this->responseElement('old', request()->all());
            $this->responseException($e);
        }

        return $this->sendResponse();
    }
}
