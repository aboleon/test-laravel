@props([
    'interventionIds' => [],
    ])

@php use App\Accessors\Programs;
 $program = Programs::getOrganizerPrintViewCollectionByInterventions($interventionIds);
 $arrows = false;
 $links = false;
 $positions = false;
@endphp


@if($program)
    <table class="table table-bordered table-hover"
           id="program-organizer"
           data-ajax="{{route('ajax')}}">
        <thead>
        <tr>
            <th>Date</th>
            <th>Session</th>
            <th>Intervention</th>
            <th>Lieu</th>
            <th>Heure début</th>
            <th>Heure fin</th>
        </tr>
        </thead>


        @include('events.manager.program.organizer.inc.print_table_body')
    </table>

@else
    <div class="alert alert-warning">
        {{__('programs.no_event_program_available')}}
    </div>
@endif