@php
    use \App\Accessors\EventManager\Grant\GrantStatAccessor;use App\Services\Pec\PecType;
@endphp
<x-event-manager-layout :event="$event">
    @section('meta_title', 'Grants > '. $event->texts->name)

    <x-slot name="header">
        <h2 class="event-h2">

            <span>GRANTS</span>
        </h2>
        <x-back.topbar.list-combo
            :event="$event"
            :create-route="route('panel.manager.event.grants.create', $event)"
            :show-create-route="$event->pec?->is_active"
        />


    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>

        <x-pec-status :event="$event"/>

        <x-datatables-mass-delete model="EventManager\Grant" name="title"
                                  question="<strong>Est-ce que vous confirmez la suppression des grants séléctionnés?</strong>"/>
        {!! $dataTable->table()  !!}


    </div>
    <div class="mt-4">
        @php
            $stats = (new GrantStatAccessor())->setEvent($event);
            $statsSummary = $stats->globalPecDistrubutionStats();
        @endphp
        <div class="row">
            <div class="col-6">

                @include('events.manager.grant.stats.global')


                @php
                    $statsSummaryGrouped = collect($statsSummary)->groupBy('grant_id')->map(function ($group) {
            return $group->groupBy('type')->map(function ($subgroup) {
                return [
                    'total_amount' => $subgroup->sum('total_amount'),
                    'total_sub_ht' => $subgroup->sum('total_sub_ht'),
                    'total_sub_vat' => $subgroup->sum('total_sub_vat'),
                    'total_amount_formatted' => number_format($subgroup->sum('total_amount') / 100, 2),
                    'total_sub_ht_formatted' => number_format($subgroup->sum('total_sub_ht') / 100, 2),
                    'total_sub_vat_formatted' => number_format($subgroup->sum('total_sub_vat') / 100, 2),
                ];
            });
        });

                @endphp


                @forelse ($event->grants as $grant)
                    @include('events.manager.grant.stats.grant')
                @empty
                @endforelse


            </div>
            @include('events.manager.dashboard.deposits-grant')
        </div>


    </div>


    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts()}}
    @endpush

</x-event-manager-layout>
