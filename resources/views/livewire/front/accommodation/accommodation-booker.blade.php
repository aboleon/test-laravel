@php
    use App\Accessors\HotelServices;
    use App\Enum\ParticipantType;
    use App\Helpers\Front\PriceHelper;
    use App\Models\EventManager\Accommodation;
    use Carbon\Carbon;
    use App\Accessors\Dates;

    $user = auth()->user();
    $account = $user?->account;
    $serviceIcons = HotelServices::getServiceNameToFrontIcon();
    $frontConfig = $event?->frontConfig;
    $eventContactAccessor = (new \App\Accessors\EventContactAccessor())->setEventContact($eventContact);

    $isOrator = $eventContactAccessor->isOrator();
    $pecEnabled = $eventContactAccessor->isPecAuthorized();
    $accountPecDates = [];


    # Get Datepicker range
    $minDate = $event->getRawOriginal('starts');
    $maxDate = $event->getRawOriginal('ends');

    $event->publishedAccommodations()->get()->each(function (Accommodation $accommodation) use (&$minDate, &$maxDate) {
        $contingentMinDate = $accommodation->contingent()->min('date');
        $contingentMaxDate = $accommodation->contingent()->max('date');
        if ($contingentMinDate) {
            $minDate = min($contingentMinDate, $minDate);
        }
        if ($contingentMaxDate) {
            $maxDate = max($contingentMaxDate, $maxDate);
        }
    });


    $dateFormat = Dates::getFrontDateFormat();
    $dateStart = Carbon::create($minDate)->format($dateFormat);
    $dateStartPlusOneDay = Carbon::create($minDate)->addDay()->format($dateFormat);
    $dateEndPlusOneDay = Carbon::create($maxDate)->addDay()->format($dateFormat);

@endphp
<div class="container position-relative">
    <h3 class="p-2 ps-0 divine-main-color-text zzbg-primary-subtle rounded-1">
        {{__('front/accommodation.accommodation')}}
    </h3>

    @if((!$frontConfig || (!$frontConfig->speaker_pay_room)) && $isOrator)
        <div class="p-0- alert alert-success">
            {{__('front/accommodation.to_benefit_free_room_contact_us1')}} <a
                href="mailto:{{ $event->admin->email }}">{{ $event->admin->email }}</a> {{__('front/accommodation.to_benefit_free_room_contact_us2')}}
        </div>
    @endif

    @if ($amend)
        <h5>{!! __('front/order.amend_title', [
        'route' => route('front.event.orders.edit', ['locale'=> app()->getLocale() , 'event' => $event->id, 'order' => $this->amendableOrderId ]),
        'dates' => $this->amendableDatesTitle
        ]) !!}</h5>
    @else

        <h5>{{__('front/accommodation.choose_date')}}</h5>
        <p class="small">{{__('front/accommodation.exclude_end')}}</p>

    @endif

    @if($topError)
        <div class="alert alert-danger">
            {{$topError}}
        </div>
    @endif

    <div class="d-flex flex-column gap-3 flex-md-row {{ $this->isAmendable ? 'd-none' : '' }}">

        <div class="d-flex gap-2 align-items-center">
            <label class="w-50px text-start text-md-end"
                   for="flatpickr-accommodation_date_start">{{ __('ui.start') }}</label>
            <x-simple-flatpickr
                id="flatpickr-accommodation_date_start"
                name="date_start"
                class="form-control w-150px"
                wire:model="searchDateStart"
                :config="[
                        'enable' => [
                            [
                                'from' => $dateStart,
                                'to' => $dateEndPlusOneDay,
                            ],
                        ],
                        'dateFormat' => $dateFormat,
                        'defaultDate' => $dateStart,
                    ]"
            />
        </div>
        <div class="d-flex gap-2 align-items-center pe-md-4">
            <label for="flatpickr-accommodation_date_end"
                   class="w-50px text-start text-md-end">
                {{ __('ui.end') }}</label>
            <x-simple-flatpickr
                id="flatpickr-accommodation_date_end"
                name="date_start"
                class="form-control w-150px"
                wire:model="searchDateEnd"
                :config="[
                        'enable' => [
                            [
                                'from' => $dateStartPlusOneDay,
                                'to' => $dateEndPlusOneDay,
                            ],
                        ],
                        'dateFormat' => $dateFormat,
                        'defaultDate' => $dateStartPlusOneDay,
                    ]"
            />
        </div>
        <div class="d-flex align-items-center mt-2">
            <button
                id="search-accommodation"
                wire:click="search"
                class="btn btn-small btn-primary">
                <i class="bi bi-search"></i> {{ __('ui.find') }}
                <x-front.livewire-ajax-spinner target="search"/>
            </button>
        </div>
    </div>


    <div class="container-accommodation mt-5">

        @php
            $showPecPrices = true;
            $pecPerNight = $searchResultInfo['pecPerNight'] ?? [];
            $hasPecPrices = ! empty($searchResultInfo['global']) && collect($searchResultInfo['global'])->flatten(2)->map(fn($item) => ! empty($item['pec_ttc']))->count();
        @endphp

        @if ($eventContactAccessor->isPecAuthorized())
            @if ($eventContactAccessor->hasAnyPecAccommodation())
                @php
                    $accountPecDates = $eventContactAccessor->getPecAccommodationDates();
                    // $showPecPrices = false;
                @endphp
                <x-mfw::alert type="info"
                              :message="__('front/accommodation.pec_hotel_limitation_reached', ['dates' => $accountPecDates ? collect($accountPecDates)->map(fn($item) => $item->format('d/m'))->join(', ') : ''])"/>
            @else
                @if ($hasPecPrices)
                    <x-mfw::alert type="info" :message="__('front/accommodation.pec_hotel_limitation')"/>
                @endif
            @endif
        @endif

        @forelse($accommodations as $accommodation)
            @php
                $availableDetails = $searchResultInfo['global'][$accommodation->id];
                $j = $accommodation->id;

                $price = 0;

                $firstPhoto = $accommodation->hotel->media->first()?->url() ;
                $services = HotelServices::getHotelServiceNames($accommodation->hotel);
                $hotel = $accommodation->hotel;
                $processingFee = $accommodation->processing_fee;
                $accommodationPec = $accommodation->pec;

            @endphp
            <div x-data="{
                    isOpen: true,
                }" class="card card-bordered mb-3">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-lg-{{ $firstPhoto ? 6 : 12 }}">
                            <h3 class="mb-0 divine-secondary-color-text">{{$hotel->name}}</h3>
                            <p class="mb-0 fs-5">{{$accommodation->title}}</p>
                            <div class="mt-3 fs-12">
                                <i class="bi bi-geo-alt-fill"></i>
                                {{$hotel->address->text_address}}
                                {{$accommodation->description}}
                            </div>

                            @if($hotel->stars)
                                <ul class="list-inline mb-0">
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
                            @endif

                            @if($price)
                                <p class="mb-3 text-light-emphasis">
                                    {!! __('front/accommodation.min_price_per_night', [
                                    'price' => PriceHelper::frontPriceWithDecimal($price)
                                    ])!!}
                                </p>
                            @endif
                            <p class="mt-2">
                                {{$hotel->description}}
                            </p>

                        </div>
                        @if($firstPhoto)
                            @includeIf('livewire.front.accommodation.hotel-image')
                        @endif

                    </div>
                    <div class="d-flex justify-content-end mt-4 mb-3 mb-sm-0">
                        <a x-show="!isOpen" href="#" @click.prevent="isOpen=!isOpen">
                            <i class="bi bi-chevron-down "></i> {{__("Afficher plus")}}
                        </a>
                        <a x-show="isOpen" href="#" @click.prevent="isOpen=!isOpen">
                            <i class="bi bi-chevron-up "></i> {{__("Afficher moins")}}
                        </a>
                    </div>
                    <div x-show="isOpen" x-transition class="row">
                        <div class="col">
                            @if($services)
                                <h5>{{__('front/accommodation.hotel_services')}}</h5>
                                <div class="small mb-2 d-flex align-items-center gap-2">

                                    @foreach($services as $service)
                                        @php
                                            $icon = $serviceIcons[strtolower($service)] ?? 'bi bi-box';
                                        @endphp
                                        <div>
                                            <i class="{{$icon}}"></i>
                                            {{$service}}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($processingFee && !$amendable)
                                <div class="text-danger fs-6">
                                    <i class="bi bi-exclamation-circle"></i>&nbsp;
                                    {{__('front/accommodation.processing_fee_per_room', ['price' => PriceHelper::frontPriceWithDecimal($processingFee)])}}
                                </div>
                            @endif


                            <div class="table-responsive border-0 mt-3">
                                <table class="table table-dark-gray align-middle table-accommodation p-4 mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="border-0 rounded-start">{{__('front/accommodation.col_room_category')}}</th>
                                        <th scope="col"
                                            class="border-0 rounded-start">{{__('front/accommodation.col_room_type')}}</th>
                                        <th scope="col"
                                            class="border-0">{{__('front/accommodation.col_nb_person')}}</th>
                                        <th scope="col"
                                            class="border-0">{{__('front/accommodation.col_accompany_details')}}</th>
                                        <th scope="col"
                                            class="border-0">{{__('front/accommodation.col_price')}}</th>
                                        <th scope="col"
                                            class="border-0">{{__('front/accommodation.col_processing_fee')}}</th>
                                        <th scope="col"
                                            class="border-0">{{__('front/accommodation.col_comments')}}</th>
                                        <th scope="col" class="border-0 rounded-end">
                                            {{__('front/accommodation.col_actions')}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accommodation->roomGroups as $roomGroup)
                                        @php
                                            $hasAtLeastOnePec = false;

                                            // Skip this room group if availability is zero or negative
                                            $availabilityForGroup = $searchResultInfo['availability'][$accommodation->id]['availability'][$searchResultInfo['start_date_sql']][$roomGroup->id] ?? 0;

                                            if ($availabilityForGroup <= 0) {
                                                continue;
                                            }

                                            if (!array_key_exists($roomGroup->id, $availableDetails)) {
                                                continue;
                                            }
                                        @endphp
                                        @foreach($roomGroup->rooms as $room)
                                            @php
                                                if (!array_key_exists($room->id, $availableDetails[$roomGroup->id])) {
                                                    continue;
                                                }
                                                $capacity = $room->capacity;
                                                $roomDetails = $availableDetails[$roomGroup->id][$room->id];

                                                $roomTotalPrice = $roomDetails['price_ttc'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <h6 class="table-responsive-title mt-2 mt-lg-0 mb-0">
                                                        {{$roomGroup->name}}
                                                    </h6>
                                                </td>
                                                <td>
                                                    <h6 class="table-responsive-title mt-2 mt-lg-0 mb-0">
                                                        {{$room->room->name}}</h6>
                                                </td>
                                                <td>
                                                    @if($capacity == 1)
                                                        1
                                                    @else
                                                        <select class="form-select form-select-sm"
                                                                wire:model="userRoomPreferences.{{$room->id}}.capacity"
                                                                id="">
                                                            @for($i = 1; $i <= $capacity; $i++)
                                                                <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($capacity == 1)
                                                        ---
                                                    @else
                                                        <textarea
                                                            placeholder="Nom, prénom et date de naissance de chaque accompagnant"
                                                            wire:model="userRoomPreferences.{{$room->id}}.accompanying_details"
                                                            class="form-control form-control-sm"
                                                            rows="3"></textarea>
                                                    @endif
                                                </td>
                                                <td class="dropup text-nowrap">
                                                    @if($showPecPrices && $pecEnabled && $roomDetails['pec_ttc'])
                                                        <span class="small text-decoration-line-through">{{$roomTotalPrice}} €</span>
                                                        <span class="text-dark">{{$roomTotalPrice - $roomDetails['pec_ttc']}} €</span>
                                                    @else
                                                        {{$roomTotalPrice}} €
                                                    @endif

                                                    @if($showPecPrices && $pecEnabled)
                                                        @php
                                                            $key = $accommodation->id . '-' . $room->id;
                                                            $hasAtLeastOnePec = false;
                                                        @endphp
                                                        @if(array_key_exists($key, $pecPerNight))
                                                            @foreach($pecPerNight[$key] as $date => $pec)
                                                                @php
                                                                    if($pec > 0){
                                                                        $hasAtLeastOnePec = true;
                                                                        break;
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        @endif

                                                        @if ($hasAtLeastOnePec)
                                                            <a href="#"
                                                               class="h6 mb-0 text-danger"
                                                               role="button"
                                                               id="dropdownShare"
                                                               data-bs-toggle="dropdown"
                                                               aria-expanded="false">
                                                                <i class="bi bi-info-circle-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-w-sm dropdown-menu-end min-w-auto shadow rounded"
                                                                aria-labelledby="dropdownShare"
                                                                style="">
                                                                <li>
                                                        <span class="small">
                                                      {{__('front/accommodation.industry_support_grant')}}
                                                            </span>
                                                                    <hr class="my-1">
                                                                </li>
                                                                @php
                                                                    $key = $accommodation->id . '-' . $room->id;
                                                                    $hasAtLeastOnePec = false;
                                                                @endphp
                                                                @if(array_key_exists($key, $pecPerNight))
                                                                    @foreach($pecPerNight[$key] as $date => $pec)
                                                                        <li>
                                                                            <span class="small">
                                                                                {{ ($searchResultInfo['excudedPecDates']->contains($date) ? 0 : $pec)  . ' € ' .  __('front/accommodation.the') . ' ' . Carbon::create($date)->format($dateFormat) }}
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                @else
                                                                    <li>
                                                            <span class="small">
                                                            Aucune prise en charge n'est disponible pour cette chambre {{$key}}.
                                                            </span>
                                                                    </li>
                                                                @endif

                                                            </ul>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(($showPecPrices && $pecEnabled && $hasAtLeastOnePec) or $amendable)
                                                        <span class="small text-decoration-line-through">{{$accommodation->processing_fee}} €</span>
                                                        <span class="text-dark">0 €</span>
                                                    @else
                                                        {{$accommodation->processing_fee}} €
                                                    @endif
                                                </td>
                                                <td>
                                                <textarea placeholder="Commentaires"
                                                          wire:model="userRoomPreferences.{{$room->id}}.comment"
                                                          class="form-control form-control-sm"
                                                          rows="3"></textarea>
                                                </td>
                                                <td>
                                                    <button
                                                        wire:click.prevent="bookStay({{$roomGroup->id}}, {{$room->id}})"
                                                        class="btn btn-sm btn-primary-soft mb-1 mb-sm-0 action-book-stay">
                                                        {{__('front/accommodation.book')}}
                                                        <x-front.livewire-ajax-spinner
                                                            target="bookStay({{$roomGroup->id}}, {{$room->id}})"/>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @empty
            @if($searchDateStart)
                @php
                    $email = \App\Accessors\EventAccessor::getAdminSubscriptionEmail($event);
                @endphp
                <div class="alert alert-info">
                    Aucun hébergement disponible pour ces dates.
                    <br>
                    @if($email)
                        Vous pouvez élargir votre recherche ou contacter
                        <a href="mailto:{{$email}}">l'organisation</a>.
                    @else
                        Vous pouvez élargir votre recherche ou contacter l'organisation.
                    @endif

                </div>
            @endif
        @endforelse
    </div>

    @push("js")
        @script
        <script>
            let currentE = null;

            $(document).ready(function () {
                $('.container-accommodation').on('click', '.action-book-stay', function (e) {
                    currentE = e;
                    return false;
                });
            });

            Livewire.on('AccommodationBooker:onBookRoomError', function (data) {
                let alert = '<div class="alert alert-danger">' + data[0] + '</div>';
                SimpleModal.create({
                    title: 'Attention',
                    body: alert,
                });
            });

            Livewire.on('AccommodationBooker:onBookRoomSuccess', function (data) {
                Trail.trigger(currentE);
                Livewire.dispatch('PopupCart.refresh');
            });
        </script>

        @endscript
    @endpush
</div>
