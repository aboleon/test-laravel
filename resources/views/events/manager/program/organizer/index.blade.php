@php use App\Accessors\GroupAccessor; @endphp
<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Programme</span> &raquo;
            <span>Organizer</span>
        </h2>
        <x-back.topbar.list-combo :event="$event" :show-create-route="false"/>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <div class="container mt-4 wg-card">
            <h4>{{__('programs.program')}}</h4>
            <x-program :event="$event" :arrows="true" :links="true" :positions="true"/>
        </div>


    </div>


</x-event-manager-layout>
