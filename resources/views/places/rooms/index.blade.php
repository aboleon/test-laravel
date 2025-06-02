<x-backend-layout>
    <x-slot name="header">
        <h2>
            Lieux &raquo;
            <span>{{ $place->name }}</span> &raquo;
            <span>{{ trans_choice('ui.rooms.label',2) }}</span>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <a class="btn btn-sm btn-warning mx-2" style="color: #333"
               href="{{  route('panel.places.edit', $place) }}">
                <i class="fa-solid fa-edit"></i>
                Lieu
            </a>
            <a class="btn btn-sm btn-success"
               href="{{ route('panel.places.rooms.create', $place) }}">
                <i class="fa-solid fa-circle-plus"></i>
                Créer</a>
            <div class="separator"></div>
        </div>
    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">

        <h2 class="legend">{!! $place->name ?? trans_choice('ui.places.label',1) !!}</h2>

        <x-mfw::response-messages/>

        <nav class="d-flex justify-content-between mb-3">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-link{{ request()->routeIs('panel.places.edit') ? ' active' : ''}}" href="{{ route('panel.places.edit', $place) }}">Fiche</a>
                <a class="nav-link{{ request()->routeIs('panel.places.rooms.index') ? ' active' : ''}}" href="{{ route('panel.places.rooms.index', $place) }}">Salles</a>
            </div>
        </nav>

        <x-datatables-mass-delete model="PlaceRoom"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush


    @push('js')
        <script>
          activateEventManagerLeftMenuItem('places');
        </script>
    @endpush
</x-backend-layout>
