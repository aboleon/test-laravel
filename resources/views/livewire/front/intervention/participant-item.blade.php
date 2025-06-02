@php
    use App\Accessors\ProgramSessions;
    use App\Enum\EventProgramParticipantStatus;
    use App\Helpers\DateHelper;use App\Models\EventManager\Program\EventProgramInterventionOrator;
    use App\Models\EventManager\Program\EventProgramSessionModerator;
    /**
     * @var EventProgramSessionModerator|EventProgramInterventionOrator $this->item
     */
    $isModerator = $this->item instanceof EventProgramSessionModerator;
    $session = $isModerator? $this->item->session:$this->item->intervention->session;
    $sm = [];
    if($isModerator){
        $sm = ProgramSessions::getPracticalSummary($this->item->session);
    }
    $sId = $this->item->id . ($isModerator? 'm':'o');


    $sessionSponsor = $session->sponsor?->name;
    $interventionSponsor = null;
    if(!$isModerator){
        $interventionSponsor = $this->item->intervention->sponsor?->name;
    }



    $date = DateHelper::getFrontDate($session->programDay->datetime_start);
    $timings = null;
    $room = $session->room?->name;
    if($isModerator){

        $earliestIntervention = $session->interventions->min('start');
        if($earliestIntervention){
            $timings  = DateHelper::getFrontHourMinute($earliestIntervention);
        }
    }else{
        $timings = DateHelper::getFrontHourMinute($this->item->intervention->start);
    }


@endphp
<div class="row mb-3">
    <div class="col-12">
        <div class="card border">

            <div class="card-body">
                <div class="mb-2">
                    <span class="d-inline">{{__('front/interventions.intervention_type')}}:</span>
                    <span class="text-dark fw-bold">
                                    @if($isModerator)
                            {{ucfirst($item->moderatorType->name)}}
                        @else
                            {{$item->specificity?->name ?? "Intervenant"}}
                        @endif
                                </span>
                </div>

                @if(!$isModerator)
                    <div class="mb-2 d-flex flex-column flex-md-row gap-0 gap-md-2 align-items-start align-items-md-baseline">
                        <div class="mb-0">
                            <span class="d-inline">{{__('front/interventions.intervention_title')}}:</span>
                            <span class="text-dark fw-bold">{{$item->intervention->name}}</span>
                        </div>
                        <p class="smaller mb-0">{{$item->intervention->description}}</p>
                    </div>
                @endif


                <div class="mb-2 d-flex flex-column flex-md-row gap-0 gap-md-2 align-items-start align-items-md-baseline">
                    <div class="mb-0">
                        <span class="d-inline">{{__('front/interventions.session')}}:</span>
                        <span class="text-dark fw-bold">{{$session->name}}</span>
                    </div>
                    <p class="smaller mb-0">{{$session->description}}</p>
                    @if($sessionSponsor)
                        <p class="small fw-bold mb-0"><u>{{__('front/interventions.sponsor')}}
                                :</u> {{$sessionSponsor}}</p>
                    @endif
                </div>

                <div class="mb-2">
                    <span class="d-inline">{{__('front/interventions.date')}}:</span>
                    <span class="text-dark fw-bold">{{$date}}</span>
                </div>

                @if($timings)
                    <div class="mb-2">
                        <span class="d-inline">{{__('front/interventions.timings')}}:</span>
                        <span class="text-dark fw-bold">{{$timings}}</span>
                    </div>
                @endif

                @if($room)
                    <div class="mb-2">
                        <span class="d-inline">{{__('front/interventions.room')}}:</span>
                        <span class="text-dark fw-bold">{{$room}}</span>
                    </div>
                @endif


                <div class="mb-2">
                    <span class="d-inline">{{__('front/interventions.duration')}}:</span>
                    <span class="text-dark fw-bold">
                        @if($isModerator)
                            {{$sm['duration']}}
                        @else
                            {{$item->intervention->intervention_timing_details??DateHelper::convertMinutesToReadableDuration($item->intervention->duration)}}
                        @endif
                                </span>
                </div>
                @if($interventionSponsor)

                    <div class="mb-2">
                        <span class="d-inline">{{__('front/interventions.sponsor')}}:</span>
                        <span class="text-dark fw-bold">{{$interventionSponsor}}</span>
                    </div>
                @endif
                <div class="mb-2 d-flex align-items-center gap-3">
                    <span class="d-inline">{{__('front/interventions.current_status')}}:</span>
                    <span class="fs-3">
                                    @switch($item->status)
                            @case(EventProgramParticipantStatus::VALIDATED->value)
                                <i class="bi bi-check-circle" style="color: green"></i>
                                <span class="text-dark fw-bold">{{__('front/interventions.accepted')}}</span>
                                @break
                            @case(EventProgramParticipantStatus::DENIED->value)
                                <i class="bi bi-x-circle" style="color:red"></i>
                                <span class="text-dark fw-bold">{{__('front/interventions.denied')}}</span>
                                @break
                            @default
                                <i class="bi bi-question-circle"
                                   style="color: blue"></i> <span
                                        class="text-dark fw-bold">{{__('front/interventions.pending')}}</span>
                                @break
                        @endswitch

                                </span>
                </div>
                <div class="mb-2 mt-4">
                    <div class="d-flex gap-1 align-items-center">
                        <a href="#"
                           wire:click.prevent="accept"
                           class="btn btn-success btn-sm smaller">{{__('front/interventions.accept')}}</a>
                        <a href="#"
                           wire:click.prevent="deny"
                           class="btn btn-danger btn-sm smaller">{{__('front/interventions.deny')}}</a>

                        <x-front.livewire-ajax-spinner target="deny,accept" />
                    </div>


                    @if(
                        EventProgramParticipantStatus::VALIDATED->value === $this->item->status &&
                        (
                            $this->item instanceof EventProgramInterventionOrator ||
                            $event->ask_video_authorization
                        )
                    )
                        <div class="mt-4">
                            Je donne mon autorisation concernant cette présentation pour:
                            {{__('front/interventions.i_give_my_authorization_for_this_presentation_for')}}
                        </div>
                        <div class="d-flex gap-3 align-items-center mb-3 mt-2">
                            @if($this->item instanceof EventProgramInterventionOrator)
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:model="pdfAuthorization"
                                           wire:change="updatePdfAuthorization"
                                           id="authorize-pdf-{{$sId}}">
                                    <label class="form-check-label" for="authorize-pdf-{{$sId}}">
                                        {{__('front/interventions.pdf_authorization')}}
                                    </label>
                                </div>
                            @endif

                            @if($event->ask_video_authorization)
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:model="videoAuthorization"
                                           wire:change="updateVideoAuthorization"
                                           id="authorize-video-{{$sId}}">
                                    <label class="form-check-label" for="authorize-video-{{$sId}}">
                                        {{__('front/interventions.video_authorization')}}
                                    </label>
                                </div>
                            @endif

                            <x-front.livewire-ajax-spinner target="updatePdfAuthorization,updateVideoAuthorization" />
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
