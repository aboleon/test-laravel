<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <h2 class="d-flex align-items-center">
            Dashboard
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <x-event-config-btn :event="$event"/>
            <a class="btn btn-secondary ms-2" href="{{ route('panel.manager.event.orders.index', $event->id) }}"><i
                    class="fa-solid fa-bars"></i> Liste des commandes</a>

            <div class="separator"></div>

            <a class="btn btn-sm btn-success ms-2"
               href="{{ route('panel.manager.event.orders.create', $event->id) }}">
                <i class="fa-solid fa-circle-plus"></i>
                Commande
            </a>
            <a class="btn btn-sm btn-warning ms-2 text-dark"
               href="{{ route('panel.manager.event.orders.create', ['event' => $event->id, 'as_orator']) }}">
                <i class="fa-solid fa-circle-plus"></i>
                Commande intervenant
            </a>

            <div class="separator"></div>
        </div>

    </x-slot>

    @php
        $error = $errors->any();
    @endphp

    <x-mfw::response-messages/>
    <x-mfw::validation-errors/>

    <div class="shadow p-4 bg-body-tertiary rounded wg-card">

        <h4>Dashboard 1 / HT</h4>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Groupe</th>
                <th>Toutes commandes PEC ou non Tout type, soldées ou non</th>
                <th>Commandes soldées PEC ou non Tout type</th>
                <th>Commandes non soldées PEC ou non Tout type</th>
                <th>Commandes soldées PEC ou non HEBERGEMENT</th>
                <th>Commandes non soldées PEC ou non HEBERGEMENT</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="category">Évènement</td>
                <td class="euro">{{ $instance->sum($global, 'total_orders') }}</td>
                <td>{{ $instance->sum($global, 'paid_orders') }}</td>
                <td>{{ $instance->sum($global, 'upaid_orders') }}</td>
                <td>{{ $instance->sum($global, 'paid_orders_accommodation') }}</td>
                <td>{{ $instance->sum($global, 'unpaid_orders_accommodation') }}</td>
            </tr>
            @foreach(\App\Enum\ParticipantType::values() as $value)
                <tr>
                    <td class="category">{{ \App\Enum\ParticipantType::translated($value) }}</td>
                    <td class="euro">{{ $instance->format($global[0]->{"total_orders_" . $value}) }}</td>
                    <td>{{ $instance->format($global[0]->{"paid_orders_" . $value}) }}</td>
                    <td>{{ $instance->format($global[0]->{"unpaid_orders_" . $value}) }}</td>
                    <td>{{ $instance->format($global[0]->{"paid_orders_accommodation_" . $value}) }}</td>
                    <td>{{ $instance->format($global[0]->{"unpaid_orders_accommodation_" . $value}) }}</td>
                </tr>
            @endforeach
            @if (isset($global[1]))
                <tr>
                    <td>Groupe non attribués</td>
                    <td>{{ $instance->format($global[1]->unassigned_amount ) }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
            </tbody>
        </table>

        <div class="row my-5">
            <div class="col-md-6">
                <h4>Orateurs / HT</h4>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>CA Total</th>
                        <th>CA Prestations</th>
                        <th>CA Hébergements</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $orators->total_orders }}</td>
                        <td>{{ $orators->total_services }}</td>
                        <td>{{ $orators->total_accommodation }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h4>PEC / HT</h4>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>CA Total</th>
                        <th>CA Prestations</th>
                        <th>CA Hébergements</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $pec->total_orders }}</td>
                        <td>{{ $pec->total_services }}</td>
                        <td>{{ $pec->total_accommodation }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <h4>Dashboard 2</h4>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Type</th>
                <th>HT</th>
                <th>TVA</th>
                <th>TTC</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th colspan="4" class="bg-dark text-white">Tout sauf PEC</th>
            </tr>
            @php
                $subdata = [];
            @endphp
            @foreach($groups as $group)
                @php
                    $dataset =  \App\Dashboards\OrdersDashboard::filterByGroup($non_pec_by_group, $group);
                    if (!$dataset) {
                        continue;
                    }
                $subdata['services'][$group] = [
                    'label' => 'Prestations ' . ($group == 'all' ? 'total' : \App\Enum\ParticipantType::translated($group)),
                    'net' => $dataset->services_total_net,
                    'vat' => $dataset->services_total_vat,
                    'total' => $dataset->services_total,
                ];
                $subdata['accommodation'][$group] = [
                    'label' => 'Hébergement ' . ($group == 'all' ? 'total' : \App\Enum\ParticipantType::translated($group)),
                    'net' => $dataset->accommodations_total_net,
                    'vat' => $dataset->accommodations_total_vat,
                    'total' => $dataset->accommodations_total,
                ];
                @endphp
            @endforeach

            @foreach($subdata as $group)
                @foreach($group as $key => $entry)
                    <tr{!! $key == 'all' ? ' class="fw-bold"' : '' !!}>
                        <td>{{ $entry['label'] }}</td>
                        <td>{{ $entry['net'] }}</td>
                        <td>{{ $entry['vat'] }}</td>
                        <td>{{ $entry['total'] }}</td>
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <th colspan="4" class="bg-dark text-white">PEC uniquement</th>
            </tr>
            @php
                $subdata = [];
            @endphp
            @foreach($groups as $group)
                @php
                    $dataset =  \App\Dashboards\OrdersDashboard::filterByGroup($pec_by_group, $group);
                    if (!$dataset) {
                        continue;
                    }
                $subdata['services'][$group] = [
                    'label' => 'Prestations ' . ($group == 'all' ? 'total' : \App\Enum\ParticipantType::translated($group)),
                    'net' => $dataset->services_total_net,
                    'vat' => $dataset->services_total_vat,
                    'total' => $dataset->services_total,
                ];
                $subdata['accommodation'][$group] = [
                    'label' => 'Hébergement ' . ($group == 'all' ? 'total' : \App\Enum\ParticipantType::translated($group)),
                    'net' => $dataset->accommodations_total_net,
                    'vat' => $dataset->accommodations_total_vat,
                    'total' => $dataset->accommodations_total,
                ];
                @endphp
            @endforeach

            @foreach($subdata as $group)
                @foreach($group as $key => $entry)
                    <tr{!! $key == 'all' ? ' class="fw-bold"' : '' !!}>
                        <td>{{ $entry['label'] }}</td>
                        <td>{{ $entry['net'] }}</td>
                        <td>{{ $entry['vat'] }}</td>
                        <td>{{ $entry['total'] }}</td>
                    </tr>
                @endforeach
            @endforeach

            </tbody>
        </table>

    </div>


</x-event-manager-layout>
