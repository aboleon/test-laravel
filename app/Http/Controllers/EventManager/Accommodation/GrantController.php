<?php

namespace App\Http\Controllers\EventManager\Accommodation;

use App\Accessors\EventManager\Availability;
use App\Accessors\EventManager\Availability\AvailabilityRecap;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Traits\Responses;

class GrantController extends Controller
{
    use Responses;

    public function edit(Event $event, Accommodation $accommodation): Renderable
    {
        return view('events.manager.accommodation.grant')->with([
            'event'         => $event,
            'accommodation' => $accommodation->load('grant'),
            'availability'  => (new Availability())->setEventAccommodation($accommodation),
            'room_groups'   => $accommodation->roomGroups->pluck('name', 'id')->toArray(),
            'blocked'       => $accommodation->blocked->groupBy('room_group_id'),
        ]);
    }

    public function update(Event $event, Accommodation $accommodation): RedirectResponse
    {
        if ( ! request()->has('date')) {
            return $this->sendResponse(message: "Aucune information à traiter");
        }

        $availability = (new Availability())
            ->setEventAccommodation($accommodation);

        $availability_recap = (new AvailabilityRecap($availability));



        for ($i = 0; $i < count(request('date')); ++$i) {
            $model = Accommodation\Grant::firstOrNew([
                'id'                     => request('id')[$i],
                'event_accommodation_id' => $accommodation->id,
            ]);

            $formatted_date = Carbon::createFromFormat('d/m/Y', request('date')[$i])->toDateString();

            $recap = $availability_recap->get($formatted_date, request('room_group_id')[$i]);
            $already_pec = $recap['confirmed']['total_pec'] + $recap['temp']['total_pec'];
            // Get existing grant bookings for this date

            //$model->event_accommodation_id = $accommodation->id;
            $model->date          = request('date')[$i];
            $model->total         = $already_pec + request('total')[$i];
            $model->room_group_id = request('room_group_id')[$i];
            $model->save();
        }

        $this->responseSuccess(__('ui.record_created'));
        $this->saveAndRedirect(route('panel.manager.event.show', $event));

        /*  } catch (\Throwable $e) {
              $this->responseException($e);
          }
  */

        return $this->sendResponse();
    }
}
