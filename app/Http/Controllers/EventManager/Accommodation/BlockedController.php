<?php

namespace App\Http\Controllers\EventManager\Accommodation;

use App\Accessors\Dictionnaries;
use App\Accessors\EventManager\Availability;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Traits\Responses;
use Throwable;

class BlockedController extends Controller
{
    use Responses;

    public function edit(Event $event, Accommodation $accommodation): Renderable
    {
        return view('events.manager.accommodation.blocked')->with([
            'event'               => $event,
            'accommodation'       => $accommodation,
            'availability'        => (new Availability())->setEventAccommodation($accommodation),
            'participation_types' => array_filter(explode(',', $accommodation->participation_types)),
            'room_groups'         => $accommodation->roomGroups->pluck('name', 'id')->toArray(),
            'services'            => $accommodation->service,
            'rooms'               => $accommodation->roomGroups->load('rooms')->pluck('rooms')->flatten(1),
            'blocked'             => $accommodation->blocked->groupBy('group_id'),
        ]);
    }

    public function update(Event $event, Accommodation $accommodation): RedirectResponse
    {
        if ( ! request()->has('group')) {
            return $this->sendResponse(message: "Aucune information à traiter");
        }

        $groups  = array_unique(request('group'));
        $orators = Dictionnaries::oratorsIds();

        $availability = (new Availability())
            ->setEventAccommodation($accommodation);

        $summary = $availability->getAvailability();
        $rooms   = $availability->getRoomGroups();

        $records = 0;

        try {
            foreach ($groups as $group) {
                $data = array_values(request($group));

                for ($i = 0; $i < count($data); ++$i) {
                    if ( ! isset($data[0]['participation_type']) or empty($data[$i]['date']) or $data[$i]['total'] < 1) {
                        $this->responseWarning("Des informations pour enregistrer une ligne pour la catégorie <b>".$rooms[$data[$i]['room_group_id']]['name']."</b> étaient incomplètes");
                        continue;
                    }

                    $thisDate = Carbon::createFromFormat('d/m/Y', $data[$i]['date'])->toDateString();
                    $avaialble = $summary[$thisDate][$data[$i]['room_group_id']] ?? 0;
                    $hasBlocked = $accommodation->blocked->filter(fn($item) => $item->date == $data[$i]['date'] && $item->room_group_id == $data[$i]['room_group_id'])->first()?->total ?: 0;

                    $totalAvailable = $avaialble + $hasBlocked;

                    if ($totalAvailable < $data[$i]['total']) {
                        $this->responseError("Le total saisi de ".$data[$i]['total']." pour ".$rooms[$data[$i]['room_group_id']]['name']." est supérieur à la disponibilité actuelle de ".$totalAvailable.".");
                        continue;
                    }

                    $model = Accommodation\BlockedRoom::firstOrNew([
                        'id'                     => $data[$i]['id'],
                        'event_accommodation_id' => $accommodation->id,
                    ]);

                    $model->date               = $data[$i]['date'];
                    $model->participation_type = implode(',', $data[0]['participation_type']);
                    $model->group_id           = $group;
                    $model->room_group_id      = $data[$i]['room_group_id'];
                    $model->total              = $data[$i]['total'];
                    $model->grant              = ! in_array($model->participation_type, $orators) ? 0 : $data[$i]['grant'];
                    $model->save();
                    $records += 1;
                }
            }

            if ($records) {
                $this->responseSuccess(__('ui.record_created'));
            }
            $this->saveAndRedirect(route('panel.manager.event.show', $event));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }
}
