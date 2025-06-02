<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Prestations au choix</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :index-route="route('panel.manager.event.choosable.index', $event)"
                :create-route="route('panel.manager.event.choosable.create', $event)"
                :model="$data"
                :delete-route="route('panel.manager.event.choosable.destroy', [$event, $data?->id??'-1'])"
                :item-name="fn($m) => 'la prestation ' . $m->title"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
            @csrf
            @if ($data->id)
                @method('PUT')
            @endif

            <div class="tab-content mt-3" id="nav-tabContent">
                <div class="row pt-3">
                    <div class="col-md-6 pe-sm-5">
                        <div class="d-flex justify-content-between">
                            <h4>Textes</h4>
                            <x-mfw::checkbox name="published" :switch="true" label="En ligne" value="1" :affected="collect($error ? old('published') : ($data->id ? $data->published : 1))"/>
                        </div>
                        <x-mfw::translatable-tabs datakey="service_texts" :fillables="$data->fillables" :model="$data"/>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @push('js')
        <script>

          activateEventManagerLeftMenuItem('choosables');
        </script>
    @endpush

</x-event-manager-layout>
