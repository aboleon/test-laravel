<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Prestations</span>
        </h2>
        <x-back.topbar.list-combo
                :event="$event"
                :show-create-route="false"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>

        <div class="wg-card mb-5">
            @include('events.manager.grant.deposit.grant.index', ['event' => $event])
        </div>
        <div class="wg-card mb-5">
            <h4>Autres cautions</h4>
            @if ($event->sellableServicesWithDeposit->isNotEmpty())
                <table class="table-bordered table">
                    <thead>
                    <tr>
                        <th>Prestation</th>
                        <th>Caution</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($event->sellableServicesWithDeposit as $item)
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->deposit->amount }} €</td>
                        <td>
                            <ul class="mfw-actions">
                                <x-mfw::edit-link :route="route('panel.manager.event.sellable.edit', ['event'=>$event, 'sellable' => $item])"/>
                            </ul>
                        </td>
                    @endforeach
                    </tbody>
                </table>
            @else
                <x-mfw::alert message="Aucune autre caution n'est demandée dans le cadre de cet évènement."/>
            @endif
        </div>
    </div>
</x-event-manager-layout>
