
@if ($serviceItems->isNotEmpty() || $accommodationItems->isNotEmpty())
    <div class="card card-body shadow p-4 align-items-start">
        <h5 class="divine-main-color-text card-title mt-3 mb-2 d-flex w-100">
            <span>{{ __('front/event.confirmation.title_front')}}</span>
            <a target="_blank" class="btn btn-sm btn-primary ms-auto" href="{{route('pdf-printer', ['type' => 'eventConfirmation', 'identifier' => $eventContact->uuid])}}"><i class="fas fa-file-pdf"></i></a>

        </h5>
    </div>
@endif

<div class="card card-body shadow p-4 align-items-start">
    <h5 class="divine-main-color-text card-title mt-3 mb-2 d-flex w-100">
        <span>{{ __('enum.pec_type.accommodation') }}</span>
        <a href="{{ route('front.event.orders.index', $event->id) }}"
           class="btn btn-sm btn-primary ms-auto">{{ __('ui.see') }}</a>

    </h5>
    @if ($items->isEmpty())
        <span>{{ __('front/dashboard.you_dont_have_reservations_yet') }}</span>
    @else
        <div class="w-100">
            @foreach($items as $item)
                @if ($item['error'])
                    <!-- {{ $item['text'] }} -->
                    @continue
                @endif
                <div class="row align-items-center">
                    <div
                        class="col-lg-6">
                        <h6 class="divine-secondary-color-text text-primary-emphasis m-0">
                            {{ $item['title'] }}
                        </h6>
                    </div>
                    @if ($item['badge'])
                        <div class="col-lg-6 text-end">
                            @foreach($item['badge'] as $badge)
                                <span class="ms-3 smaller badge rounded-pill {{ $badge['class'] ?? '' }}">{{ $badge['text'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="small">
                    {!! $item['text'] !!}
                </p>
            @endforeach
        </div>

    @endif

    <div class="d-flex justify-content-start align-items-center w-100 gap-2 mt-3">
        <a href="{{ route('front.event.accommodation.edit', $event->id) }}"
           class="btn btn-sm btn-outline-primary">{{ __('front/ui.buy') }}</a>
    </div>
</div>
