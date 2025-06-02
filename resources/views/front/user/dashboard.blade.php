<x-front-logged-in-layout :event="$event">
    @php
        $hasIntervention = $eventContact->programInterventionOrators->count() > 0 || $eventContact->programSessionModerators()->count() > 0;
        $showTransport = \App\Accessors\Front\FrontCache::canAccessTransport();

$group = $eventContact?->participationType?->group;
    @endphp
    <div class="masonry-container">
        <div @class([
            'card card-body shadow py-4 px-3 align-items-start',
            'bg-warning' => $eventTimeline['passed'],
            'bg-info' => $eventTimeline['coming'],
            'bg-success' => $eventTimeline['ongoing'],
        ])>
            <h5 class="card-title m-0">
                {!!
                    match($eventTimeline['state']) {
                        'passed' => __('front/dashboard.event_passed_by_x_days', ['days' => $eventTimeline['passed_since'], 'day' => trans_choice('ui.day', $eventTimeline['passed_since'])]),
                        'coming' => __('front/dashboard.only_x_days_left_before_event', ['days' => $eventTimeline['days_to_event'], 'day' => trans_choice('ui.day', $eventTimeline['days_to_event'])]),
                        'ongoing' => __('front/dashboard.ongoing_event')
                    };
                !!}

            </h5>
        </div>

        <x-front.remaining-payments :amount="$orderAmount" :event_id="$event->id"/>


        <x-front.dashboard-card
            title="{{__('front/dashboard.services_and_registrations')}}"
            :show-title-action="$serviceItems->isNotEmpty()"
            :title-action="[
                    'url' => route('front.event.orders.index', $event),
                    'text' => 'Voir',
                ]"
            notBusyText="{!! __('front/dashboard.you_dont_have_additional_services_yet') !!}"
            seeActionUrl="{{  route('front.event.service_and_registration.edit', $event) }}"
            readMoreUrl="{{  route('front.event.service_and_registration.edit', $event) }}"
            :items="$serviceItems"
            see-action-word="Acheter"
            :show-see-action="true"
        />

        @include('front.dashboard.deposits-card', ['items' => collect(), 'event' => $event, 'grantDeposit' => $grantDeposit] )



        @include('front.dashboard.accommodation-card', ['items' => $accommodationItems, 'event' => $event] )


        @if($hasIntervention)
            <x-front.dashboard-card
                title="{{__('front/dashboard.interventions')}}"
                notBusyText="{!! __('front/dashboard.you_dont_have_interventions_yet') !!}"
                readMoreUrl="{{route('front.event.intervention.edit', $event)}}"
                seeActionUrl="{{route('front.event.intervention.edit', $event)}}"
                :items="$moderatorOratorItems"
                :show-see-action="true"
            />
        @endif

        @if($showTransport)

            <x-front.dashboard-card
                title="{{__('front/dashboard.transports')}}"
                notBusyText="{!! __('front/dashboard.you_dont_have_additional_services_yet') !!}"
                seeActionUrl="{{route('front.event.transport.edit', $event)}}"
                :items="$transportItems"
                :show-see-action="true"
            />
        @endif

        @if($invitationItems->isNotEmpty())
            <x-front.dashboard-card
                title="Invitations"
                notBusyText="{!! __('front/dashboard.you_dont_have_invitations_yet') !!}"
                readMoreUrl="{{route('front.event.invitation.edit', $event)}}"
                :items="$invitationItems"
                :show-see-action="false"
            />
        @endif




        {{-- DEAD CODE LEGACY --}}
        {{--
        @if(false)

            <x-front.dashboard-card
                :showPicto="false"
                title="{{__('front/dashboard.call_for_abstracts')}}"
                pictoUrl="{{asset('front/images/pictograms/prof.png')}}"
                notBusyText="{!! __('front/dashboard.you_dont_have_additional_services_yet') !!}"
                readMoreUrl="{{route('front.event.account.edit', $event)}}"
                :items="[
                    [
                        'title' => 'Titre de l\'abstract',
                        'text' => 'Date de soumission, type d\'abstract',
                        'badge' => [
                            'class' => 'bg-info rounded-pill',
                            'text' => 'En cours de relecture',
                        ],
                    ],
                    [
                        'title' => 'Titre de l\'abstract',
                        'text' => 'Date de soumission, type d\'abstract',
                        'badge' => [
                            'class' => 'bg-success rounded-pill',
                            'text' => 'Validé',
                        ],
                    ],
                ]"
                :bottomActions="[
                    [
                        'type' => 'button',
                        'class' => 'btn-info',
                        'title' => 'Déposer un abstract',
                    ],
                ]"
                :show-see-action="false"
            />

        @endif
        --}}
    </div>

    @push('css')
        <style>
            .masonry-container {
                column-width: 25em;
                column-gap: 1em;
            }

            .masonry-container .card {
                break-inside: avoid-column;
                margin-bottom: 1em;
            }

        </style>
    @endpush

</x-front-logged-in-layout>
