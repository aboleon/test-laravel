<x-front-logged-in-group-manager-v2-layout>

    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2>{{ __('front/groups.members_attribution') }}</h2></div>
        <div class="col-lg-4 text-lg-end text-center">
            <a class="btn btn-sm btn-primary"
               href="{{ route('front.event.group.members', ['locale' => $locale, 'event' => $event->id]) }}">
                {{ __('front/groups.dashboard_group_members') }}
            </a>
        </div>
    </div>
    <hr>

    <x-mfw::response-messages/>
    <form action="" id="attributions-form"
          data-event-id="{{ $event->id }}"
          data-group-id="{{ \App\Accessors\Front\FrontCache::getEventGroup()->group_id }}"
          data-origin="{{ \App\Enum\OrderOrigin::FRONT->value }}"
          data-order-id="{{ Crypt::encryptString(implode(',', array_column($groupAccessor->stockAccommodationQuery(), 'order_id'))) }}">
        <div class="mfw-line-separator mb-4 pb-2"></div>

        @if (!in_array($type, App\Enum\OrderCartType::defaultCarts()))
            <x-mfw::notice class="alert alert-danger" :message="__('front/groups.attribution_limitation')"/>
        @else
            @include('front.orders.attributions.'.$type)
        @endif
    </form>

    @include('orders.attributions.messages')

    @push('callbacks')
        <script src="{{ asset('js/orders/order_attribution.class.js') }}"></script>
    @endpush

</x-front-logged-in-group-manager-v2-layout>
