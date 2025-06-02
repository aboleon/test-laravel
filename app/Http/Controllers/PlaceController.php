<?php

namespace App\Http\Controllers;

use App\DataTables\PlaceDataTable;
use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Models\PlaceAddress;
use App\Traits\DataTables\MassDelete;
use App\Traits\Locale;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Responses;
use Throwable;

class PlaceController extends Controller
{
    use Locale;
    use MassDelete;
    use ValidationTrait;
    use Responses;

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(PlaceDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render('places.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create(): Renderable
    {
        return view('places.edit')->with([
            'data' => new Place,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PlaceRequest $request
     * @return RedirectResponse
     */
    public function store(PlaceRequest $request): RedirectResponse
    {
        $this->ensureDataIsValid($request, 'place');
        $this->ensureDataIsValid($request, 'wa_geo');

        if ($this->hasErrors()) {
            return $this->sendResponse();
        }

        try {
            $place = Place::create($this->validatedData('place'));
            $place->address()->save(
                new PlaceAddress($this->validatedData('wa_geo'))
            );

            $place->processMedia();

            $this->responseSuccess(__('ui.record_created'));
            $this->redirect_to = route('panel.places.edit', $place);
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Place $place
     * @return Renderable
     */
    public function edit(Place $place): Renderable
    {
        return view('places.edit')->with([
            'data' => $place,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PlaceRequest $request
     * @param Place $place
     * @return RedirectResponse
     */
    public function update(PlaceRequest $request, Place $place): RedirectResponse
    {
        $this->ensureDataIsValid($request, 'place');
        $this->ensureDataIsValid($request, 'wa_geo');

        if ($this->hasErrors()) {
            return $this->sendResponse();
        }
        try {
            $place->update($this->validatedData('place'));
            $place->address()->update($this->validatedData('wa_geo'));

            $place->processMedia();

            $this->responseSuccess(__('ui.record_updated'));
            $this->redirect_to = route('panel.places.edit', $place);
            $this->saveAndRedirect(route('panel.places.index'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Place $place
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Place $place): RedirectResponse
    {


        $nbRooms = $place->rooms->count();
        $denyDeletion = $nbRooms > 0;

        /**
         * tmp: we might want to activate the kind of code below,
         * but if we do, it only makes sense to also check for other types of relationships
         * between places/rooms and other models.
         * So for now, we just check if there are rooms, and that's a sufficient criteria to deny deletion.
         *
         * The main idea is that they don't want to delete a place if it is linked to another model,
         * such as a session, or an accommodation,...
         */
        if (!'later_divine_complains_about_this_system') {
            foreach ($place->rooms as $room) {
                if ($room->sessions()->exists()) {
                    $denyDeletion = true;
                    break;
                }
            }
        }

        if ($denyDeletion) {
            $this
                ->redirectRoute('panel.places.index')
                ->whitout("object")
                ->responseError("Ce lieu ne peut pas être supprimé car il est lié à d'autres données.");
            return $this->sendResponse();
        }


        return (new Suppressor($place))
            ->remove()
            ->redirectRoute('panel.places.index')
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object')
            ->sendResponse();
    }
}
