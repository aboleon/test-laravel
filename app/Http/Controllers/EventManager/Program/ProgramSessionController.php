<?php

namespace App\Http\Controllers\EventManager\Program;

use App\Accessors\Dictionnaries;
use App\Accessors\PlaceRooms;
use App\Accessors\ProgramInterventions;
use App\Accessors\ProgramSessionModerators;
use App\DataTables\EventProgramSessionDataTable;
use App\Enum\EventProgramParticipantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\Program\EventProgramSessionRequest;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramIntervention;
use App\DataTables\View\EventProgramSessionView;
use App\Models\EventManager\Program\EventProgramSession;
use App\Models\PlaceRoom;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use Throwable;

class ProgramSessionController extends Controller
{

    use Responses;

    private EventProgramSession $pgSession;

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $dataTable = new EventProgramSessionDataTable($event);

        $sessionCount = EventProgramSessionView::where('event_id', $event->id)->count();

        return $dataTable->render('events.manager.program.session.datatable.index', [
            'event' => $event,
            'sessionCount' => $sessionCount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event): Renderable
    {

        $moderatorsInfoArray = [];
        $allOldData = request()->old();
        if (!empty($allOldData)) {
            $moderatorIds = Arr::get($allOldData, 'intervention.moderators', []);
            $moderatorInfos = Arr::get($allOldData, 'intervention.moderators_info', []);
            $moderatorsCombined = $this->combineModeratorsInfo($moderatorIds, $moderatorInfos);
            if (null !== $moderatorIds) {
                $moderators = EventContact::whereIn('id', $moderatorIds)->get();
                $moderatorsInfoArray = ProgramSessionModerators::getModeratorsInfo($moderators, $moderatorsCombined);
            }
        }

        return view('events.manager.program.session.edit')->with([
            'data' => new EventProgramSession(),
            'place_id' => $event->place_id,
            'place_rooms' => PlaceRooms::selectableArray($event->place_id),
            'moderators' => $moderatorsInfoArray,
            'event' => $event,
            'route' => route('panel.manager.event.program.session.store', $event),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventProgramSessionRequest $request, Event $event)
    {
        try {

            $this->pgSession = new EventProgramSession();
            $this->hydrateModel($request, $event);

            $this->redirect_to = route('panel.manager.event.program.session.edit', ['event'=> $event, 'session' => $this->pgSession]);
            $this->responseSuccess(__('ui.record_created'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, EventProgramSession $session): Renderable
    {
        $allOldData = request()->old();
        $moderators = $session->moderators;
        $moderatorsCombined = [];
        $placeId = null;
        $placeRooms = [];
        if (null !== $session->place_room_id) {
            $placeId = PlaceRoom::where('id', $session->place_room_id)->first()->place_id;
            $placeRooms = PlaceRooms::selectableArray($placeId);
        }


        if (!empty($allOldData)) {
            $moderatorIds = Arr::get($allOldData, 'intervention.moderators');
            $moderatorInfos = Arr::get($allOldData, 'intervention.moderators_info');
            $moderatorsCombined = $this->combineModeratorsInfo($moderatorIds, $moderatorInfos);
            if (null !== $moderatorIds) {
                $moderators = EventContact::whereIn('id', $moderatorIds)->get();
            }
        }
        $moderatorsInfoArray = ProgramSessionModerators::getModeratorsInfo($moderators, $moderatorsCombined, $session->id);

        return view('events.manager.program.session.edit')->with([
            'data' => $session,
            'place_id' => $placeId,
            'place_rooms' => $placeRooms,
            'moderators' => $moderatorsInfoArray,
            'event' => $event,
            'route' => route('panel.manager.event.program.session.update', [$event, $session]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventProgramSessionRequest $request, Event $event, EventProgramSession $session)
    {
        try {
            $this->pgSession = $session;
            $this->hydrateModel($request, $event, true);
            $this->redirect_to = route('panel.manager.event.program.session.edit', ['event'=> $event, 'session' => $this->pgSession]);
            $this->responseSuccess(__('ui.record_updated'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, EventProgramSession $session)
    {
        return (new Suppressor($session))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('La session est supprimée.'))
            ->redirectTo(route('panel.manager.event.program.session.index', $event))
            ->sendResponse();
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function hydrateModel(EventProgramSessionRequest $request, Event $event, $isUpdate = false)
    {
        $session = $request->input('session.main');
        $extra = $request->input('extra', []);
        $fakeIntervention = $extra['fake_intervention'] ?? 0;
        $fakeInterventionDuration = $extra['fake_intervention_duration'] ?? 0;
        $catering = $extra['catering'] ?? 0;
        $cateringDuration = $extra['catering_duration'] ?? 0;

        $session_texts = $request->get('session_texts');

        $interventionModeratorIds = $request->input('intervention.moderators');
        $interventionModeratorInfos = $request->input('intervention.moderators_info');


        $duration = $session['duration'] ?? null;
        if ('0' === $duration) {
            $duration = null;
        }

        //--------------------------------------------
        // position
        //--------------------------------------------
        // Check if the session is being updated and if its context (event_program_day_room_id or place_room_id) has changed
        $isContextChanged = $isUpdate
            && (
                $this->pgSession->event_program_day_room_id !== (int)$session['event_program_day_room_id'] ||
                $this->pgSession->place_room_id !== (int)$session['place_room_id']
            );

        // Only determine a new position if it's a new session or if its context has changed
        if (!$isUpdate || $isContextChanged) {

            $existingMaxPosition = EventProgramSession::where('event_program_day_room_id', $session['event_program_day_room_id'])
                ->where('place_room_id', $session['place_room_id'])
                ->max('position');
            $newPosition = is_null($existingMaxPosition) ? 1 : $existingMaxPosition + 1;
            $this->pgSession->position = $newPosition;
        }


        //--------------------------------------------
        // other fields
        //--------------------------------------------
        $this->pgSession->event_program_day_room_id = $session['event_program_day_room_id'];
        $this->pgSession->is_online = $session['is_online'] ?? null;
        $this->pgSession->name = $session_texts['name'];
        $this->pgSession->description = $session_texts['description'] ?? null;
        $this->pgSession->session_type_id = $session['session_type_id'];
        $this->pgSession->duration = $duration;
        $this->pgSession->place_room_id = $session['place_room_id'];
        $this->pgSession->sponsor_id = $session['sponsor_id'] ?? null;
        $this->pgSession->is_visible_in_front = $session['is_visible_in_front'] ?? null;
        if (!$isUpdate) {
            $this->pgSession->is_catering = ($catering) ? 1 : null;
            $this->pgSession->is_placeholder = ($fakeIntervention) ? 1 : null;
        }

        $this->pgSession->save();

        //--------------------------------------------
        // sync moderators
        //--------------------------------------------
        if (null !== $interventionModeratorIds) {
            $syncData = $this->combineModeratorsInfo($interventionModeratorIds, $interventionModeratorInfos);
            $this->pgSession->moderators()->sync($syncData);
        } elseif ($isUpdate && null === $interventionModeratorIds) {
            $this->pgSession->moderators()->sync([]);
        }


        if ($fakeIntervention) {
            $this->createCateringOrTmpIntervention($event, [
                "fr" => "Intervention par défaut",
                "en" => "Default intervention",
            ], $fakeInterventionDuration, true);
        }
        if ($catering) {
            $this->createCateringOrTmpIntervention($event, $session_texts['name'], $cateringDuration);
        }


    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function combineModeratorsInfo(array $ids, array $details): array
    {
        $syncData = [];
        if ($ids) {
            foreach ($ids as $index => $moderatorId) {
                $syncData[$moderatorId] = [
                    'status' => $details['status'][$index] ?? EventProgramParticipantStatus::PENDING->value,
                    'allow_video_distribution' => $details['allow_video_distribution'][$index] ?? 0,
                    'moderator_type_id' => $details['moderator_type_id'][$index] ?? array_keys(Dictionnaries::selectValues('program_moderator_type'))[0],
                ];
            }
        }
        return $syncData;
    }


    private function createCateringOrTmpIntervention(Event $event, array $translatable, int $duration, bool $isPlaceholder = false)
    {
        $specificityId = Dictionnaries::dictionnary('program_intervention_types')->entries?->first()?->id;

        $i = new EventProgramIntervention();
        $i->event_program_session_id = $this->pgSession->id;
        $i->position = 1;
        $i->name = $translatable;
        $i->specificity_id = $specificityId;
        $i->duration = $duration;
        if ($isPlaceholder) {
            $i->is_placeholder = 1;
        } else {
            $i->is_catering = 1;
        }

        $i->save();
        ProgramInterventions::refreshStartEndTimes($event);
    }
}
