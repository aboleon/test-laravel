<?php

namespace App\Http\Controllers\EventManager\Program;

use App\Accessors\Places;
use App\Accessors\ProgramInterventions;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Program\EventProgramDayRoom;
use Illuminate\Support\Carbon;
use MetaFramework\Services\Validation\ValidationTrait;

class ProgramContainerController extends Controller
{
    use ValidationTrait;

    private string $programError = '';
    private Event $event;

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        return view('events.manager.program.container.index')->with([
            'event' => $event,
            'route' => route('panel.manager.event.program.containers.update', $event),
            'places' => Places::selectableArray(),
        ]);
    }

    public function update(Event $event)
    {
        $this->event = $event;
        $this->syncProgramDays();

        if (!empty($this->programError)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $this->programError]);
        }
        return $this->sendResponse();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function syncProgramDays()
    {
        //--------------------------------------------
        // sync
        //--------------------------------------------
        if ($this->event->has_program) {


            $dayRooms = request("event.program_day_rooms");
            //--------------------------------------------
            // validation
            //--------------------------------------------
            if ($dayRooms) {
                foreach ($dayRooms['room_id'] as $v) {
                    if (!$v) {
                        $this->programError = "Veuillez sélectionner une salle pour chaque jour (onglet Programme).";
                        return;
                    }
                }
                foreach ($dayRooms['place_id'] as $v) {
                    if (!$v) {
                        $this->programError = "Veuillez sélectionner un lieu pour chaque jour (onglet Programme).";
                        return;
                    }
                }
            }


            if (!$dayRooms) {
                EventProgramDayRoom::where('event_id', $this->event->id)->delete();
                return;
            }


            $oldDays = $this->event->programDays->keyBy(function ($dr) {
                return $this->programDayRoomToIdentifier($dr->event_id, $dr->datetime_start, $dr->room_id);
            })->all();


            $newDaysKeys = array_map(function ($day, $hour, $roomId) {
                return $this->programDayRoomToIdentifier(
                    $this->event->id,
                    Carbon::createFromFormat('d/m/Y H:i', $day . ' ' . $hour)->format('Y-m-d H:i') . ":00",
                    $roomId);
            }, $dayRooms['day'], $dayRooms['hour'], $dayRooms['room_id']);

            $newDaysValues = array_map(function ($day, $hour, $roomId) {
                return [
                    'event_id' => $this->event->id,
                    'datetime_start' => Carbon::createFromFormat('d/m/Y H:i', $day . ' ' . $hour)->format('Y-m-d H:i') . ":00",
                    'room_id' => $roomId,
                ];
            }, $dayRooms['day'], $dayRooms['hour'], $dayRooms['room_id']);
            $newDays = array_combine($newDaysKeys, $newDaysValues);


            $daysToDelete = array_diff_key($oldDays, $newDays);
            $daysToCreate = array_diff_key($newDays, $oldDays);


            $hasChanged = false;
            foreach ($oldDays as $k => $oldDay) {
                if (array_key_exists($k, $newDays)) {
                    $oldDay->update($newDays[$k]);
                    $hasChanged = true;
                }
            }

            foreach ($daysToDelete as $day) {
                $day->delete();
                $hasChanged = true;
            }
            if (!empty($daysToCreate)) {
                $this->event->programDays()->createMany($daysToCreate);
                $hasChanged = true;
            }
            if ($hasChanged) {
                ProgramInterventions::refreshStartEndTimes($this->event);
            }

        }
        else{
            $this->programError = "Le programme n'est pas activé pour cet événement.";
        }
    }

    private function programDayRoomToIdentifier(int $eventId, string $datetimeStart, int $roomId): string
    {
        return "e:{$eventId};dt:{$datetimeStart};r:{$roomId};";
    }
}
