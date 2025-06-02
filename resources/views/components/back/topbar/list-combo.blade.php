@props([
    'routePrefix' => '',
    'createRoute' => null,
    'wrap' => true,
    'createBtnDevMark' => false,
    'showCreateRoute' => true,
    'event' => null,
    'export' => false,
    'exportRoute' => null
])
@if($wrap)
    <div class="d-flex align-items-center gap-1" id="topbar-actions">
        @endif

        @if($export)
            <a class="btn btn-sm btn-primary me-2 export"
               href="{{$exportRoute ?? '#'}}">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Exporter</a>
        @endif
        @if($event)
            <x-back.topbar.edit-event-btn :event="$event" />
        @endif

        @if($showCreateRoute)
            @php
                $createRoute = $createRoute ?? route($routePrefix . '.create');
            @endphp
            <x-back.topbar.new-btn :route="$createRoute" :show-dev-mark="$createBtnDevMark" />
            <x-back.topbar.separator />
        @endif
        @if($wrap)
    </div>
@endif
