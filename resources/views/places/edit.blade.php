<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.places.label',2) }}
        </h2>

        <x-back.topbar.edit-combo
                :model="$data"
                route-prefix="panel.places"
                :item-name="fn($model) => 'le lieu ' . $model->name"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <h2 class="legend">{!! $data->name ?? trans_choice('ui.places.label',1) !!}</h2>

        <x-mfw::validation-banner/>
        <nav class="d-flex justify-content-between mb-3">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                @if ($data->id)
                    <a class="nav-link{{ request()->routeIs('panel.places.edit') ? ' active' : ''}}" href="{{ route('panel.places.edit', $data) }}">Fiche</a>
                    <a class="nav-link{{ request()->routeIs('panel.places.rooms.index') ? ' active' : ''}}" href="{{ route('panel.places.rooms.index', $data) }}">Salles</a>
                    <x-mfw::save-alert />
                @endif
            </div>
        </nav>
        <form method="post" action="{{ $data->id ? route('panel.places.update', $data->id) : route('panel.places.store') }}" id="wagaia-form">
            @csrf
            @if($data->id)
                @method('put')
            @endif
            @include('places.form')
        </form>
    </div>

    @include('lib.tinymce')


    @push('js')
        <script>
          activateEventManagerLeftMenuItem('places');
        </script>
    @endpush

</x-backend-layout>>
