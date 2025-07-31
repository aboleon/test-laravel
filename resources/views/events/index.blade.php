<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('events.label',2) }}
        </h2>

        <x-back.topbar.list-combo route-prefix="panel.events" />

    </x-slot>


    <div class="wg-tabs nav nav-tabs">
        <a href="{{route('panel.events.index')}}"
           class="nav-link tab @if(!$archived) active @endif">{{__('ui.active')}}</a>
        <a href="{{route('panel.events.archived')}}"
           class="nav-link tab @if($archived) active @endif">{{__('ui.archived')}}</a>
    </div>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="Event" name="texts.name"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>
