<?php

namespace App\Http\Controllers;

use App\DataTables\PlaceRoomDataTable;
use App\Traits\DataTables\MassDelete;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Actions\Translator;
use App\Http\Requests\RoomRequest;
use App\Models\Place;
use App\Models\PlaceRoom;
use App\Traits\Locale;
use MetaFramework\Traits\Responses;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PlaceRoomController extends Controller
{
    use Locale;
    use MassDelete;
    use Responses;

    /**
     * Display a listing of the resource.
     *
     * @param Place $place
     */
    public function index(Place $place): JsonResponse|View
    {
        $dataTable = new PlaceRoomDataTable($place);
        return $dataTable->render('places.rooms.index', ['place' => $place]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Place $place
     * @return Renderable
     */
    public function create(Place $place): Renderable
    {
        $room = new PlaceRoom;
        $room->place()->associate($place);
        return view('places.rooms.edit')->with([
            'place' => $place,
            'data' => $room,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Place $place
     * @param RoomRequest $request
     * @return RedirectResponse
     */
    public function store(Place $place, RoomRequest $request): RedirectResponse
    {

        try {
            $data = $request->validated();
            $room = $place->rooms()->create($data);
            (new Translator($room))
                ->update();

            $this->syncRelations($room);

            $this->responseSuccess(__('ui.record_created'))
                ->redirectTo(route('panel.places.rooms.index', $place));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param PlaceRoom $room
     * @return Renderable
     */
    public function edit(PlaceRoom $room): Renderable
    {
        return view('places.rooms.edit')->with([
            'place' => $room->place,
            'data' => $room,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PlaceRoom $room
     * @param RoomRequest $request
     * @return RedirectResponse
     */
    public function update(PlaceRoom $room, RoomRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $room->fill($data);
            $room->save();

            (new Translator($room))
                ->update();

            $this->syncRelations($room);

            $this->responseSuccess(__('ui.record_created'))
                ->redirectTo(route('panel.rooms.edit', $room));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PlaceRoom $room
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(PlaceRoom $room): RedirectResponse
    {
        $place = $room->place;

        return (new Suppressor($room))
            ->remove()
            ->redirectTo(route('panel.places.rooms.index', $place))
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object')
            ->sendResponse();
    }
    public function syncRelations(PlaceRoom $room): void
    {
        // Setups
        $room->setup()->delete();


        if (request()->filled('place_room_setup')) {

            $data = [];
            $input = request('place_room_setup');

            for ($i = 0; $i < count($input['name']); ++$i) {
                $data[] = [
                    'name' => $input['name'][$i],
                    'capacity' => $input['capacity'][$i],
                    'description' => $input['description'][$i],
                ];
            }
            $room->setup()->createMany($data);

        }
    }
}
