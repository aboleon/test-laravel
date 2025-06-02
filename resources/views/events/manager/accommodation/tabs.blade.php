<nav class="d-flex justify-content-between mb-3 align-items-center">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.show') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.show', [$event, $accommodation]) }}">Récap</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.rooms.groups') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.rooms.groups', [$event, $accommodation]) }}">Récap bloc groupes</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.edit') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.edit', [$event, $accommodation]) }}">Hébergement</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.rooms.edit') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.rooms.edit', [$event, $accommodation]) }}">Configuration Chambres</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.rooms.contingent') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.rooms.contingent', [$event, $accommodation]) }}">Contingent</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.rooms.blocked') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.rooms.blocked', [$event, $accommodation]) }}">Chambres bloquées</a>
        <a class="nav-link{{ request()->routeIs('panel.manager.event.accommodation.rooms.grant') ? ' active' : ''}}" href="{{ route('panel.manager.event.accommodation.rooms.grant', [$event, $accommodation]) }}">Chambres Grant</a>
    </div>

    <x-mfw::notice message="Dates&nbsp;: {{ $event->starts }} au {{ $event->ends }}"/>
</nav>
@push('js')
    <script>
        $(function() {
            $('ul.side-menu li.accommodation').addClass('current-page');
        });
    </script>
@endpush
