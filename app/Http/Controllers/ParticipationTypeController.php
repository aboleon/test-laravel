<?php

namespace App\Http\Controllers;

use App\Accessors\ParticipationTypes;
use App\DataTables\ParticipationTypeDataTable;
use App\Http\Requests\ParticipationTypeRequest;
use App\Models\ParticipationType;
use App\Traits\DataTables\MassDelete;
use App\Traits\Locale;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Validation\ValidationTrait;
use Throwable;

class ParticipationTypeController extends Controller
{
    use Locale;
    use MassDelete;
    use ValidationTrait;

    public function index(ParticipationTypeDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render('dictionnary.participation_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create(): Renderable
    {
        return view('dictionnary.participation_type.edit')->with([
            'data'  => new ParticipationType(),
            'route' => route('panel.participationtypes.store'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ParticipationTypeRequest  $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(ParticipationTypeRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->manageDefaultState($request);

            $model = ParticipationType::create($request->validated());
            $this->responseSuccess(__('ui.record_created'));
            $this->redirect_to = route('panel.participationtypes.edit', $model);
            $this->saveAndRedirect(route('panel.participationtypes.index'));
            $this->resetCache();

            $this->checkForDefaultParticipationType();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ParticipationType  $participationtype
     *
     * @return Renderable
     */
    public function edit(ParticipationType $participationtype): Renderable
    {
        return view('dictionnary.participation_type.edit')->with([
            'data'  => $participationtype,
            'route' => route('panel.participationtypes.update', $participationtype),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ParticipationTypeRequest  $request
     * @param  ParticipationType         $participationtype
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(ParticipationTypeRequest $request, ParticipationType $participationtype): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->manageDefaultState($request);

            $participationtype->update($request->validated());
            $this->responseSuccess(__('ui.record_updated'));
            $this->redirect_to = route('panel.participationtypes.edit', $participationtype);
            $this->saveAndRedirect(route('panel.places.index'));
            $this->resetCache();

            $this->checkForDefaultParticipationType();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ParticipationType  $participationtype
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(ParticipationType $participationtype): RedirectResponse
    {
        $deleted = (new Suppressor($participationtype))
            ->remove()
            ->redirectRoute('panel.participationtypes.index')
            ->responseSuccess(__('ui.record_deleted'))
            ->whitout('object');

        $this->resetCache();

        return $deleted->sendResponse();
    }

    public function resetCache(): void
    {
        cache()->forget('participation_types');
        cache()->forget('default_participation_type');
        cache()->forget('orators');
    }

    private function checkForDefaultParticipationType(): void
    {
        if ( ! ParticipationTypes::defaultId()) {
            $this->responseWarning("Vous n'avez pas de type de participation par défaut");
        }
    }

    private function manageDefaultState(ParticipationTypeRequest $request): void
    {
        if ($request->validated('default') == 1) {
            ParticipationType::where('default', 1)->update(['default' => 0]);
        }
    }
}
