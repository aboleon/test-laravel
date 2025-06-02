<?php

namespace App\Http\Controllers\EventManager\Program;

use App\Accessors\ProgramInterventionOrators;
use App\Accessors\ProgramInterventions;
use App\DataTables\EventProgramInterventionDataTable;
use App\Enum\EventProgramParticipantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\Program\EventProgramInterventionRequest;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramIntervention;
use App\DataTables\View\EventProgramInterventionView;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use Throwable;

class ProgramInterventionController extends Controller
{

    use Responses;

    private EventProgramIntervention $pgIntervention;
    private Event $event;

    public function index(Event $event)
    {
        $sessionId = request()->get('session');
        $dataTable = new EventProgramInterventionDataTable($event, $sessionId);

        $interventionCount = EventProgramInterventionView::where('event_id', $event->id)->count();

        return $dataTable->render('events.manager.program.intervention.datatable.index', [
            'event' => $event,
            'interventionCount' => $interventionCount
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event): Renderable
    {
        $sessionId = request()->get('session');
        $intervention = new EventProgramIntervention();
        $oratorsInfoArray = [];

        $allOldData = request()->old();
        if (!empty($allOldData)) {
            $sessionId = Arr::get($allOldData, 'intervention.main.event_program_session_id');
            $oratorIds = Arr::get($allOldData, 'intervention.orators', []);
            $oratorInfos = Arr::get($allOldData, 'intervention.orators_info', []);
            $oratorsCombined = $this->combineOratorsInfo($oratorIds, $oratorInfos);
            if (null !== $oratorIds) {
                $orators = EventContact::whereIn('id', $oratorIds)->get();
                $oratorsInfoArray = ProgramInterventionOrators::getOratorsInfo($orators, $oratorsCombined);
            }
        }


        $intervention->event_program_session_id = $sessionId;


        return view('events.manager.program.intervention.edit')->with([
            'data' => $intervention,
            'orators' => $oratorsInfoArray,
            'event' => $event,
            'route' => route('panel.manager.event.program.intervention.store', $event),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventProgramInterventionRequest $request, Event $event)
    {
       try {

            $this->pgIntervention = new EventProgramIntervention();
            $this->event = $event;
            $this->process($request);

           $params = [
               'event' => $event,
               'intervention' => $this->pgIntervention,
           ];

           if(request()->has('from_session') && request('from_session') == $this->pgIntervention->event_program_session_id){
               $params['session'] = request('from_session');
           }

           $this->redirect_to = route('panel.manager.event.program.intervention.edit', $params);
           $this->responseSuccess(__('ui.record_created'));

        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, EventProgramIntervention $intervention): Renderable
    {

        $allOldData = request()->old();
        $orators = $intervention->orators;
        $oratorsCombined = [];


        if (!empty($allOldData)) {
            $oratorIds = Arr::get($allOldData, 'intervention.orators');
            $oratorInfos = Arr::get($allOldData, 'intervention.orators_info');
            $oratorsCombined = $this->combineOratorsInfo($oratorIds, $oratorInfos);
            if (null !== $oratorIds) {
                $orators = EventContact::whereIn('id', $oratorIds)->get();
            }

        }


        $oratorsInfoArray = ProgramInterventionOrators::getOratorsInfo($orators, $oratorsCombined, $intervention->id);


        return view('events.manager.program.intervention.edit')->with([
            'data' => $intervention,
            'orators' => $oratorsInfoArray,
            'event' => $event,
            'route' => route('panel.manager.event.program.intervention.update', [$event, $intervention]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventProgramInterventionRequest $request, Event $event, EventProgramIntervention $intervention)
    {
        try {
            $this->pgIntervention = $intervention;
            $this->event = $event;
            $this->process($request, true);

            $params = [
                'event' => $event,
                'intervention' => $this->pgIntervention,
            ];

            if(request()->has('from_session') && request('from_session') == $this->pgIntervention->event_program_session_id){
                $params['session'] = request('from_session');
            }

            $this->redirect_to = route('panel.manager.event.program.intervention.edit', $params);
            $this->responseSuccess(__('ui.record_updated'));

        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     * @throws \Exception
     */
    public function destroy(Event $event, EventProgramIntervention $intervention)
    {
        $params = [
            'event' => $event,
        ];

        if(request()->has('session')){
            $params['session'] = request('session');
        }

        return (new Suppressor($intervention))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__("L'intervention est supprimée."))
            ->redirectTo(route('panel.manager.event.program.intervention.index', $params))
            ->sendResponse();
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function process(EventProgramInterventionRequest $request, $isUpdate = false)
    {
        $intervention = $request->input('intervention.main');
        $interventionOratorIds = $request->input('intervention.orators');
        $interventionOratorInfos = $request->input('intervention.orators_info');

        $intervention_texts = $request->get('intervention_texts');

        //--------------------------------------------
        // position
        //--------------------------------------------
        // Check if the session is being updated and if its context (event_program_day_room_id or place_id) has changed
        $isContextChanged = $isUpdate
            && (
                $this->pgIntervention->event_program_session_id !== (int)$intervention['event_program_session_id']
            );

        // Only determine a new position if it's a new session or if its context has changed
        if (!$isUpdate || $isContextChanged) {

            $existingMaxPosition = EventProgramIntervention::where('event_program_session_id', $intervention['event_program_session_id'])
                ->max('position');
            $newPosition = is_null($existingMaxPosition) ? 1 : $existingMaxPosition + 1;
            $this->pgIntervention->position = $newPosition;
        }


        //--------------------------------------------
        // other fields
        //--------------------------------------------
        $this->pgIntervention->event_program_session_id = $intervention['event_program_session_id'];
        $this->pgIntervention->sponsor_id = $intervention['sponsor_id'] ?? null;
        $this->pgIntervention->name = $intervention_texts['name'];
        $this->pgIntervention->description = $intervention_texts['description'] ?? null;
        $this->pgIntervention->internal_comment = $intervention['internal_comment'];

        $this->pgIntervention->specificity_id = $intervention['specificity_id'] ?? null;
        $this->pgIntervention->duration = $intervention['duration'];
        $this->pgIntervention->intervention_timing_details = $intervention['intervention_timing_details'];


        $this->pgIntervention->save();
        ProgramInterventions::refreshStartEndTimes($this->event);


        //--------------------------------------------
        // sync orators
        //--------------------------------------------
        if (null !== $interventionOratorIds) {
            $syncData = $this->combineOratorsInfo($interventionOratorIds, $interventionOratorInfos);
            $this->pgIntervention->orators()->sync($syncData);
        } elseif ($isUpdate && null === $interventionOratorIds) {
            $this->pgIntervention->orators()->sync([]);
        }


    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function combineOratorsInfo(array $ids, array $details): array
    {
        $syncData = [];
        if ($ids) {
            foreach ($ids as $index => $oratorId) {
                $syncData[$oratorId] = [
                    'status' => $details['status'][$index] ?? EventProgramParticipantStatus::PENDING->value,
                    'allow_video_distribution' => $details['allow_video_distribution'][$index] ?? 0,
                    'allow_pdf_distribution' => $details['allow_pdf_distribution'][$index] ?? 0,
                ];
            }
        }
        return $syncData;
    }


}
