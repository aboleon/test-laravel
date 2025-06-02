<?php

namespace App\Http\Controllers;

use App\DataTables\EstablishmentDataTable;
use App\Enum\EstablishmentType;
use App\Http\Requests\EstablishmentRequest;
use App\Models\Establishment;
use App\Traits\DataTables\MassDelete;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use Throwable;

class EstablishmentController extends Controller
{
    use MassDelete;

    public function index(EstablishmentDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render('establishments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create(): Renderable
    {
        return view('establishments.edit')->with([
            'data' => new Establishment,
            'types' => EstablishmentType::translations(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EstablishmentRequest $request
     * @return RedirectResponse
     */
    public function store(EstablishmentRequest $request): RedirectResponse
    {
        try {

            $data = $request->validated()['establishment'];
            unset($data['country']);

            $establishment = Establishment::create($data);
            $this->responseSuccess(__('ui.record_created'));
            $this->redirect_to = route('panel.establishments.edit', $establishment);
            $this->saveAndRedirect(route('panel.establishments.index'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Establishment $establishment
     * @return Renderable
     */
    public function edit(Establishment $establishment): Renderable
    {
        return view('establishments.edit')->with([
            'data' => $establishment,
            'types' => EstablishmentType::translations(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EstablishmentRequest $request
     * @param Establishment $establishment
     * @return RedirectResponse
     */
    public function update(EstablishmentRequest $request, Establishment $establishment): RedirectResponse
    {
        try {

            $data = $request->validated()['establishment'];
            unset($data['country']);

            $establishment->update($data);

            $this->responseSuccess(__('ui.record_updated'));
            $this->redirect_to = route('panel.establishments.edit', $establishment);
            $this->saveAndRedirect(route('panel.establishments.index'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Establishment $establishment
     * @throws \Exception
     */
    public function destroy(Establishment $establishment): RedirectResponse
    {
        return (new Suppressor($establishment))
            ->remove()
            ->redirectRoute('panel.establishments.index')
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object')
            ->sendResponse();
    }
}
