@php
    use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;

    $userId = auth()->id();
    $hasIntervention = $eventContact->programInterventionOrators->count() > 0 || $eventContact->programSessionModerators()->count() > 0;

    $showTransport = \App\Accessors\Front\FrontCache::canAccessTransport();
    $invitations = EventContactSellableServiceChoosables::getEventContactChoosables($event, $eventContact);
    $services = \App\Accessors\Front\FrontCache::getNonChoosableServicesCount();

    $permissions = [
        'allowDashboard',
        'allowMyAccount',
        'allowServices',
        'allowAccommodation',
        'allowIntervention',
        'allowInvitation',
        'allowTransport',
        'allowOrders',
        'allowLogout',
    ];

    $permissions = array_fill_keys($permissions, true);

    if ($isConnectedAsManager && $groupManager) {
        $intent = \App\Accessors\Front\FrontCache::getGroupManagerParams('intent');

        if($intent) {
            $permissions = array_fill_keys(array_keys($permissions), false);
            switch ($intent) {
                case "general-info":
                    $permissions['allowMyAccount'] = true;
                break;
                case "services":
                    $permissions['allowServices'] = true;
                    $permissions['allowAccommodation'] = true;
                break;
                case "accommodations":
                    $permissions['allowAccommodation'] = true;
                break;
            }
        }
    }

@endphp

    <!-- Left sidebar START -->
<div class="col-xl-3">
    <!-- Responsive offcanvas body START -->
    <div class="offcanvas-xl offcanvas-end" tabindex="-1" id="offcanvasSidebar">
        <!-- Offcanvas header -->
        <div class="offcanvas-header bg-light">
            <h5 class="offcanvas-title"
                id="offcanvasNavbarLabel">{{__('front/ui.menu_title')}}</h5>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    data-bs-target="#offcanvasSidebar"
                    aria-label="{{ __('ui.close') }}"></button>
        </div>
        <!-- Offcanvas body -->
        <div class="offcanvas-body p-3 p-xl-0">
            <div class="bg-dark divine-menu-font bg-user-left-sidebar border rounded-3 p-3 w-100">
                <!-- Dashboard menu -->
                <div class="list-group list-group-dark list-group-borderless collapse-list">
                    @if($permissions['allowDashboard'])
                        <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.dashboard', $event->id),
                        ])
                           href="{{ route('front.event.dashboard', $event->id) }}"><i
                                class="bi bi-ui-checks-grid fa-fw me-2"></i>{{__('front/ui.dashboard')}}
                        </a>
                    @endif

                    @if($permissions['allowMyAccount'])
                        <a class="list-group-item"
                           data-bs-toggle="collapse"
                           href="#collapase_personal_info"
                           role="button"
                           aria-controls="collapase_personal_info">

                            <i class="bi bi-person-rolodex"></i>
                            {{__('front/ui.my_account')}}
                        </a>
                    @endif

                    @php
                        $routeIsInfo = request()->routeIs('front.event.account.edit', $event->id);
                        $routeIsCredentials = request()->routeIs('front.event.credentials.edit', $event->id);
                        $routeIsCoordinates = request()->routeIs('front.event.coordinates.edit', $event->id);
                        $routeIsDocuments = request()->routeIs('front.event.documents.edit', $event->id);

                    @endphp

                    <ul @class([
                        'nav flex-column collapse',
                        'show' => $routeIsInfo  || $routeIsCoordinates || $routeIsCredentials || $routeIsDocuments,
                        ]) id="collapase_personal_info" data-bs-parent="#navbar-sidebar" style="">


                        <a @class([
                        'list-group-item',
                        'active' => $routeIsInfo,
                        ]) href="{{ route('front.event.account.edit', $event->id) }}"><i
                                class="bi bi-person-circle fa-fw me-2"></i> {{__('front/ui.my_personal_info')}}
                        </a>

                        <a @class([
                        'list-group-item',
                        'active' => $routeIsCoordinates,
                        ]) href="{{ route('front.event.coordinates.edit', $event->id) }}"><i
                                class="bi bi-crosshair fa-fw me-2"></i> {{__('front/ui.my_coordinates')}}
                        </a>


                        <a @class([
                        'list-group-item',
                        'active' => $routeIsDocuments,
                        ]) href="{{ route('front.event.documents.edit', $event->id) }}"><i
                                class="bi bi-file-earmark-text fa-fw me-2"></i> {{__('front/ui.my_documents')}}

                        </a>

                        <a @class([
                        'list-group-item',
                        'active' => $routeIsCredentials,
                        ]) href="{{ route('front.event.credentials.edit', $event->id) }}"><i
                                class="bi bi-lock fa-fw me-2"></i> {{__('front/ui.my_credentials')}}
                        </a>


                    </ul>


                    @if($permissions['allowServices'])
                        @if($services)
                            <a @class([
                        'list-group-item text-nowrap',
                        'active' => request()->routeIs('front.event.service_and_registration.edit', $event->id),
                        ]) href="{{ route('front.event.service_and_registration.edit', $event->id) }}"><i
                                    class="bi bi-person-lines-fill fa-fw me-2"></i> {{__('front/ui.services_and_registrations')}}
                            </a>
                        @endif
                    @endif

                    @if($permissions['allowAccommodation'])
                        <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.accommodation.edit', $event->id),
                        ]) href="{{ route('front.event.accommodation.edit', $event->id) }}"><i
                                class="bi bi-house-fill fa-fw me-2"></i> {{__('front/ui.accommodations')}}
                        </a>
                    @endif


                    @if($permissions['allowIntervention'])
                        @if($hasIntervention)
                            <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.intervention.edit', $event->id),
                        ]) href="{{ route('front.event.intervention.edit', $event->id) }}"><i
                                    class="bi bi-megaphone fa-fw me-2"></i> {{__('front/ui.interventions')}}
                            </a>
                        @endif
                    @endif

                    @if($permissions['allowTransport'])
                        @if($showTransport)
                            <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.transport.edit', $event->id),
                        ]) href="{{ route('front.event.transport.edit', $event->id) }}"><i
                                    class="bi bi-taxi-front-fill fa-fw me-2"></i> {{__('Transports')}}
                            </a>
                        @endif

                        @if($invitations->isNotEmpty())
                            <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.invitation.edit', $event->id),
                        ]) href="{{ route('front.event.invitation.edit', $event->id) }}">
                                <i class="bi bi-person-vcard fa-fw me-2"></i> {{__('front/ui.invitations')}}
                            </a>
                        @endif
                    @endif

                    @if($permissions['allowOrders'])
                        <a @class([
                        'list-group-item',
                        'active' => request()->routeIs('front.event.orders.index', $event->id),
                        ]) href="{{ route('front.event.orders.index', $event->id) }}">
                            <i class="bi bi-basket me-2"></i> {{__('front/ui.orders')}}
                        </a>
                    @endif

                    @if(false)
                        <a class="list-group-item text-nowrap"
                           href="#"><i
                                class="bi bi-calendar-event fa-fw me-2"></i>{{ str_replace('$event', $eventName, __('front/ui.event_register_or_book_a_room')) }}
                        </a>
                        <a class="list-group-item"
                           href="#"><i
                                class="bi bi-file-earmark-text me-2"></i> {{ str_replace('$event', $eventName, __('front/ui.event_docs')) }}
                        </a>
                        <a class="list-group-item"
                           href="#"><i
                                class="bi bi-journals fa-fw me-2"></i>{{__('front/ui.additional_services')}}
                        </a>
                        <a class="list-group-item"
                           href="#"><i class="bi bi-trash fa-fw me-2"></i>Supprimer
                            mon compte</a>
                    @endif
                    @if($isMainContact)
                        <a class="list-group-item text-warning" href="{{route('front.event.login-as-group-manager', [
                            'event' => $event->id,
                        ])}}"><i class="bi bi-people-fill"></i> {{ __('front/account.manager_account') }}</a>
                    @endif

                    @if($permissions['allowLogout'])
                        @include('front.shared.logout')
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
