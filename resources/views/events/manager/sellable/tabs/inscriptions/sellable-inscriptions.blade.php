<div class="row p-3" x-data="checkboxManager()">
    <div class="col-md-7 pe-sm-5">
        <h4 class="mt-4">Inscriptions pour les prestations</h4>
        <style>
            table.dt.dataTable {
                width: 100% !important;
            }

            table th[title=Actions] {
                width: 200px !important;
            }
        </style>

        @php
            $dataTable = new App\DataTables\EventSellableSalesDataTable($data);
        @endphp

        {!! $recap->table() !!}

        @include('lib.datatable')
        @push('js')
            {!! $recap->scripts() !!}
        @endpush

    </div>
    <div class="col-md-5 pe-sm-5">

        @php
            $attributions = \App\Models\Order\Attribution::where([
                'shoppable_type' => \App\Enum\OrderCartType::SERVICE->value,
                'shoppable_id' => $data->id,
                ])->with(['eventContact.account','order.group'])->get();

        @endphp
        <h4 class="mt-4">Attributions des prestations</h4>

        <table class="table table-sm">
            <thead>
            <tr>
                <th class="ps-3">Commande</th>
                <th class="ps-3">Groupe</th>
                <th class="ps-3">Participant</th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            @forelse($attributions as $attribution)
                <tr>
                    <td class="ps-3">
                        {{ $attribution->order_id }}
                    </td>
                    <td class="ps-3">
                        {{ $attribution->order->group->names() }}
                    </td>
                    <td class="ps-3">
                        {{ $attribution->eventContact->account->names() }}
                    </td>
                    <td class="text-end">

                        <a target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Dashboard"
                           class="btn btn-sm btn-default"
                           href="{{ route("panel.manager.event.event_contact.edit", ['event' => $data->event_id, 'event_contact' => $attribution->event_contact_id]) }}">D</a>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Commande" target="_blank"
                           class="btn btn-sm btn-secondary"
                           href="{{ route('panel.manager.event.orders.edit', ['event' => $data->event_id, 'order' => $attribution->order_id])}}">C</a>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Attribitions" target="_blank"
                        class="btn btn-sm btn-dark"
                           href="{{ route('panel.manager.event.orders.attributions', ['event' => $data->event_id, 'order' => $attribution->order_id, 'type'=> \App\Enum\OrderCartType::SERVICE->value]) }}">A</a>

                    </td>
                </tr>
            @empty

            @endforelse
            </tbody>
        </table>


    </div>
</div>
