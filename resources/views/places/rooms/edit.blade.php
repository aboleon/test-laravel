<x-backend-layout>
    @php
        $error = $errors->any();
    @endphp
    <x-slot name="header">
        <h2>
            Lieux &raquo;
            <span>{{ $place->name }}</span>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">

            <a class="btn btn-sm btn-secondary mx-2"
               href="{{  route('panel.places.rooms.index', $place) }}">
                <i class="fa-solid fa-bars"></i>
                Index
            </a>
            <a class="btn btn-sm btn-warning mx-2" style="color: #333"
               href="{{  route('panel.places.edit', $place) }}">
                <i class="fa-solid fa-edit"></i>
                Lieu
            </a>

            @if ($data->id)
                <a class="btn btn-sm btn-success"
                   href="{{ route('panel.places.rooms.create',$place) }}">
                    <i class="fa-solid fa-circle-plus"></i>
                    Créer</a>
                <a class="btn btn-danger ms-2" href="#"
                   data-bs-toggle="modal"
                   data-bs-target="#destroy_{{ $data->id }}">
                    <i class="fa-solid fa-trash"></i>
                    Supprimer
                </a>
            @endif
            <div class="separator"></div>

            <x-save-btns/>

        </div>
    </x-slot>

    @if ($data->id)
        <x-mfw::modal :route="route('panel.rooms.destroy', $data)"
                      question="Supprimer le lieu {{ $data->name }} ?"
                      reference="destroy_{{ $data->id }}"/>
    @endif

    <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-4">

            <h2 class="legend">{!! $data->name ?? trans_choice('ui.rooms.label',1) !!}</h2>

            <x-mfw::validation-banner/>

            <form method="post" action="{{ $data->id ? route('panel.rooms.update', $data) : route('panel.places.rooms.store', $place) }}" id="wagaia-form">
                @csrf
                @if($data->id)
                    @method('put')
                @endif

                @include('places.rooms.form')

                <x-mfw::btn-save/>
            </form>
        </div>
    </div>

    @include('lib.tinymce')

    @push('js')
        <script>
          activateEventManagerLeftMenuItem('places');
        </script>
    @endpush


</x-backend-layout>
