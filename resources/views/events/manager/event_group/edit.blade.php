<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <h2 class="event-h2">
            <span>Groupe | Tableau de bord</span>
        </h2>
        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>
            <x-back.topbar.separator />
            <a class="btn btn-sm btn-secondary" href="{{ route('panel.manager.event.event_group.index', [
                        'event' => $event,
                    ]) }}">
                <i class="fa-solid fa-bars"></i>
                Index
            </a>

        </div>
    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::validation-banner />
        <x-mfw::response-messages />

        <h3 class="mb-3">
            {{ $eventGroup->group->name }}
            <x-front.debugmark :title="$eventGroup->group->id" />
        </h3>
        <hr class="mb-4">

        <x-tab-cookie-redirect id="event_group" selector="#event_group-nav-tab .mfw-tab" />

        @include('events.manager.event_group.inc.tabs')

        <div class="tab-content mt-3" id="nav-tabContent">
            @include('events.manager.event_group.tabs.dashboard')
            @include('events.manager.event_group.tabs.general')
            @include('events.manager.event_group.tabs.group')
            @include('events.manager.event_group.tabs.event_group')
            @include('events.manager.event_group.tabs.rooms')
            @include('events.manager.event_group.tabs.history')
        </div>
    </div>

    @push('js')
        <script>
          $(document).ready(function() {
            activateEventManagerLeftMenuItem('event-groups');
          });
        </script>
    @endpush


</x-event-manager-layout>
