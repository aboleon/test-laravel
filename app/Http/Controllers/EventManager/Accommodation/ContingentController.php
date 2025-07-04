<?php

namespace App\Http\Controllers\EventManager\Accommodation;

use App\Accessors\EventManager\Availability;
use App\Accessors\EventManager\Availability\AvailabilityRecap;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Accommodation\Contingent;
use App\Models\EventManager\Accommodation\ContingentConfig;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use MetaFramework\Traits\Responses;
use Throwable;

class ContingentController extends Controller
{
    use Responses;

    private Accommodation $accommodation;

    public function edit(Event $event, Accommodation $accommodation): Renderable
    {
        $this->accommodation = $accommodation;

        return view('events.manager.accommodation.contingent')->with([
            'event'         => $event,
            'accommodation' => $this->accommodation,
            'room_groups'   => $this->getRoomGroups(),
            'services'      => $this->accommodation->service,
            'rooms'         => $this->accommodation->roomGroups->load('rooms')->pluck('rooms')->flatten(1),
        ]);
    }

    public function update(Event $event, Accommodation $accommodation): RedirectResponse
    {
        $this->accommodation = $accommodation;
        $roomGroups          = $this->getRoomGroups();

        if ( ! request()->has('row_key')) {
            return $this->sendResponse(message: "Aucune information à traiter");
        }

        $hasAnyStock = $accommodation->contingent->isNotEmpty();

        if ($hasAnyStock) {
            $availabilty = (new Availability())->setEventAccommodation($accommodation);
            $summary     = (new AvailabilityRecap($availabilty));
        }
        DB::beginTransaction();
        try {
            foreach (request('row_key') as $key) {
                $contingent = request()->has($key.'.id') ? Contingent::find(request($key.'.id')) : new Contingent();

                $requestDate      = request($key.'.date');
                $requestRoomGroup = request($key.'.room_group_id');
                $formatted_date   = Carbon::createFromFormat('d/m/Y', $requestDate)->toDateString();
                $total            = request($key.'.total');

                if ($hasAnyStock) {
                    $recap = $summary->get($formatted_date, $requestRoomGroup);

                    $treshold
                        = (isset($recap['blocked']) ? ($recap['blocked']['total_pec'] + $recap['blocked']['total']) : 0) + // Les bloqués
                        (isset($recap['confirmed']) ? ($recap['confirmed']['total'] - $recap['confirmed']['total_pec']) : 0) +
                        (isset($recap['temp']) ? ($recap['temp']['total'] - $recap['temp']['total_pec']) : 0);

                    //de($recap, 'Recap for '.$formatted_date.' '.$requestRoomGroup);
                    if ($total < $treshold) {
                        $this->responseError("Le contingent pour la catégorie ".$roomGroups[$requestRoomGroup]." pour la date de ".$requestDate." ne peut pas être inférieur à ".$treshold);
                        continue;
                    }
                }

                $contingent->fill([
                    'event_accommodation_id' => $accommodation->id,
                    'room_group_id'          => $requestRoomGroup,
                    'date'                   => $requestDate,
                    'total'                  => $total,
                ])->save();

                foreach (request($key.'.rooms') as $row_key => $room) {
                    if ($row_key == 'undefined' or $row_key == 'nullable') {
                        $row_key = null;
                    }
                    $model = ContingentConfig::updateOrCreate(
                        ['contingent_id' => $contingent->id, 'room_id' => $row_key],
                        [
                            'published'      => isset($room['published']) ? 1 : null,
                            'pec'            => isset($room['pec']) ? 1 : null,
                            'pec_allocation' => $room['pec-allocation'] ?: 0,
                            'service_id'     => $room['service'] ?: null,
                            'sell'           => $room['sell'] ?: 0,
                            'buy'            => $room['buy'] ?: 0,
                        ],
                    );

                    $model->syncFromPlural('code_article', $model->id);
                }
            }
            DB::commit();
            $this->responseSuccess(__('ui.record_created'));
            $this->saveAndRedirect(route('panel.manager.event.show', $event));
        } catch (Throwable $e) {
            $this->responseException($e);
            DB::rollBack();
        }

        return $this->sendResponse();
    }

    private function getRoomGroups(): array
    {
        return $this->accommodation->roomGroups->pluck('name', 'id')->filter()->toArray();
    }
}
