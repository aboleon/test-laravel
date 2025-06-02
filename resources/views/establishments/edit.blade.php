<x-backend-layout>

    @pushonce('css')
        {!! csscrush_inline(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
    @endpushonce
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.establishments.label',2) }}
        </h2>

        <x-back.topbar.edit-combo
                route-prefix="panel.establishments"
                :model="$data"
                :item-name="fn($model) => 'l\'établissement ' . $model->name"
        />


    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <h2 class="legend">{!! $data->name ?? trans_choice('ui.establishments.label',1) !!}</h2>

        <x-mfw::validation-banner />
        <x-mfw::validation-errors />
        <x-mfw::response-messages />

        <form method="post"
              action="{{ $data->id ? route('panel.establishments.update', $data->id) : route('panel.establishments.store') }}"
              id="wagaia-form"
              autocomplete="off">
            @csrf
            @if($data->id)
                @method('put')
            @endif
            @include('establishments.form')

            @push('css')
                {!! csscrush_tag(public_path('css/establishments.css')) !!}
            @endpush
            @push('js')
                <script src="{{ asset('js/establishment_search.js') }}"></script>
            @endpush
        </form>
    </div>


    @push('js')
        <script>
          activateEventManagerLeftMenuItem('establishments');
        </script>
    @endpush

</x-backend-layout>
