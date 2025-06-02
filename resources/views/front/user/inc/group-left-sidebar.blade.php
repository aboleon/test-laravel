@php
    use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;use App\Accessors\EventManager\SellableAccessor;

    $hasIntervention = $eventContact->programInterventionOrators->count() > 0 || $eventContact->programSessionModerators()->count() > 0;
    $showTransport = \App\Accessors\Front\FrontCache::canAccessTransport();
    $invitations = EventContactSellableServiceChoosables::getEventContactChoosables($event, $eventContact);
    $services = SellableAccessor::getEventContactPublishedNonChoosableServices($event, $eventContact);

@endphp

<div class="col-xl-3">
    <div class="offcanvas-xl offcanvas-end" tabindex="-1" id="offcanvasSidebar">
        <div class="offcanvas-header bg-light">
            <h5 class="offcanvas-title"
                id="offcanvasNavbarLabel">{{__('front/ui.menu_title')}}</h5>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    data-bs-target="#offcanvasSidebar"
                    aria-label="{{ __('ui.close') }}"></button>
        </div>
        <div class="offcanvas-body p-3 p-xl-0">
            <div class="bg-dark divine-menu-font bg-user-left-sidebar border rounded-3 p-3 w-100">
                <div class="list-group list-group-dark list-group-borderless collapse-list">

                    <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.dashboard', $event->id),
                        ])
                       href="{{ route('front.event.group.dashboard', [
                          'event' => $event,
                      ]) }}"><i class="bi bi-ui-checks-grid fa-fw me-2"></i>{{__('front/ui.dashboard')}}
                    </a>


                    <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.group.members', $event->id),
                        ]) href="{{ route('front.event.group.members', $event->id) }}">
                        <i class="bi bi-people-fill me-2"></i> Membres du groupe
                    </a>


                    <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.group.buy', $event->id),
                        ]) href="{{ route('front.event.group.buy', $event->id) }}">
                        <i class="bi bi-bag me-2"></i> Mes achats
                    </a>

                    <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.group.orders', $event->id),
                        ]) href="{{ route('front.event.group.orders', $event->id) }}">
                        <i class="bi bi-receipt me-2"></i>
                        {{__('front/ui.orders')}}
                    </a>

                    <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.attributions.index', $event->id),
                        ]) href="{{ route('front.event.group.attributions.index', $event->id) }}">
                        <i class="bi bi-person-fill-check me-2" style="font-size: 18px"></i>
                        Attributions
                    </a>

                    @if($isMainContact)
                        <a class="list-group-item text-warning" href="{{route('front.event.login-as-user', [
                            'event' => $event->id,
                        ])}}"><i class="bi bi-person-circle"></i> {{ __('front/account.individual_account') }}</a>
                    @endif

                    @include('front.shared.logout')

                </div>
            </div>
        </div>
    </div>
</div>
