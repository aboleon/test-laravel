<x-front-logged-in-group-manager-v2-layout>

    <h2>{{ __('front/groups.group_orders') }}</h2>

    <div class="alert alert-light mb-5" role="alert">
        {{ __('front/groups.attribution_notice') }}
    </div>

    @if ($services->isNotEmpty())
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3>{{ \App\Enum\OrderCartType::translated(\App\Enum\OrderCartType::default()) }}</h3>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a class="btn btn-sm btn-primary"
                   href="{{ route('front.event.group.attributions.edit', ['event' => $event->id, 'type' => App\Enum\OrderCartType::SERVICE->value]) }}">
                    Gérer les attributions des prestations</a>
            </div>
        </div>

        <hr/>
        <table class="table">
            <thead>
            <tr>
                <th class="text-dark">Prestation</th>
                <th class="text-end text-dark">Quantité</th>
                <th class="text-end text-dark">Attributions</th>
                <th class="text-end text-dark">Restants</th>
            </tr>
            </thead>
            <tbody>

            @foreach($services as $item)
                <tr>
                    <td>{{ json_decode($item->service_name)->{$locale} }}</td>
                    <td class="text-end">{{ $item->ordered }}</td>
                    <td class="text-end">{{ $item->attributed }}</td>
                    <td class="text-end">{{ $item->ordered - $item->attributed }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br><br>
    @endif


    @if ($accommodation->isNotEmpty())
        <div class="row align-items-center mt-5">
            <div class="col-lg-8">
                <h3>Hébergement</h3>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a class="btn btn-sm btn-primary"
                   href="{{ route('front.event.group.attributions.edit', ['event' => $event->id, 'type' => 'accommodation']) }}">
                    {{ __('front/groups.manage_accommodation_attributions') }}
                </a>
            </div>
        </div>

        <hr/>
        @foreach($accommodation as $item)
            @php
                $hotel = $item->first()->first();
            @endphp
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <h5 class="fs-6 fw-bold py-2 px-3 badge rounded-pill text-bg-warning">{{ $hotel->hotel_name }}</h5>
                        @if($hotel->stars)
                            <ul class="list-inline ms-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <li class="list-inline-item me-0 small">
                                        @if ($i <= $hotel->stars)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    </li>
                                @endfor
                            </ul>
                    </div>

                    <div class="fs-12">
                        <i class="bi bi-geo-alt-fill"></i>
                        {{ $hotel->hotel_address }}
                    </div>
                    @endif
                </div>

            </div>
            <table class="table">
                <thead>
                <tr>
                    <th class="text-dark">Date</th>
                    <th class="text-dark">Type</th>
                    <th class="text-end text-dark">{{ __('ui.quantity') }}</th>
                    <th class="text-end text-dark">Attributions</th>
                    <th class="text-end text-dark">{{ trans_choice('front/order.remaining',2) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($item as $date => $entries)
                    <tr>
                        <td rowspan="{{ $entries->count()+1 }}">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}</td>
                    </tr>
                    @foreach($entries as $item)
                        @php
                        $attributed = $accommodationAttributions->filter(fn($a) => $a->shoppable_id == $item->room_id && $a->configs['date'] == $date)->sum('quantity');
                    @endphp
                    <tr>
                        <td>
                            {{ json_decode($item->room)->{$locale} }} x {{ $item->capacity }}p.
                            / {{ json_decode($item->room_category)->{$locale} }}
                        </td>
                        <td class="text-end">{{ $item->total_quantity }}</td>
                        <td class="text-end">{{ $attributed }}</td>
                        <td class="text-end">{{ $item->total_quantity - $attributed }}</td>
                    </tr>
                @endforeach
                @endforeach
            </table>
        @endforeach
    @endif

</x-front-logged-in-group-manager-v2-layout>
