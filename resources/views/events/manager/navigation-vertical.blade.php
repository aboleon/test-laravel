<nav>
    <!-- Primary Navigation Menu -->
    <div>
        <!-- Logo -->
        <div class="d-flex justify-content-center align-items-center px-3 py-4"
             style="background: #1A1A1A">
            <a href="{{ route('panel.dashboard') }}">
                <x-application-mark />
            </a>
        </div>
        <div class="nav-event-header">{{ $event->texts?->name ?: 'Non titré' }} <br /> <span class="small">du {{ $event->starts }} au  {{ $event->ends }}</span></div>
        <div id="sidebar-menu" class="main_menu_side bg-white main_menu">
            <ul class="nav side-menu">
                <x-mfw::nav-header-link title="Récapitulatif"
                                        icon="fas fa-chart-pie"
                                        :route="route('panel.manager.event.show', $event->id)" />

                <li class="sh-blue-grey vertical-menu-item-participation">
                    <x-mfw::nav-opening-header title="Participation" icon="fas fa-users" />
                    <ul class="nav child_menu">
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'all'])  }}"
                                         title="Participants"
                                         className="sh-blue-grey item-event-contacts" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index_with_order', ['event' => $event->id, 'group' => 'all', 'withOrder' => 'yes'])  }}"
                                         title="Inscrits"
                                         className="sh-blue-grey item-event-contacts-with-order" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index_with_order', ['event' => $event->id, 'group' => 'all', 'withOrder' => 'no'])  }}"
                                         title="Sans prestation"
                                         className="sh-blue-grey item-event-contacts-without-order" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'congress'])  }}"
                                         title="Congressites" class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.pecorder.index', $event->id)  }}"
                                         title="PEC"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'industry'])  }}"
                                         title="Industriels" class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'orator'])  }}"
                                         title="Intervenants" class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.event_group.index', ['event' => $event->id])  }}"
                                         title="Groupes"
                                         className="sh-blue-grey item-event-groups" />
                        <x-mfw::nav-link route="#" title="Exposants" class="sh-blue-grey" />

                    </ul>
                </li>

                <x-mfw::nav-header-link title="PRESTATIONS"
                                        icon="fas fa-coffee"
                                        class="sh-blue-grey standalone-sellables"
                                        :route="route('panel.manager.event.sellable.index', $event->id)" />

                <x-mfw::nav-header-link :title="__('ui.accommodations')"
                                        icon="fas fa-hotel"
                                        :route="route('panel.manager.event.accommodation.index', $event->id)"
                                        class="accommodation sh-blue-grey" />

                <x-mfw::nav-header-link title="Transports"
                                        icon="fas fa-plane"
                                        class="sh-blue-grey standalone-transports"
                                        route="{{  route('panel.manager.event.transport.index', $event->id) }}" />


                <x-mfw::nav-header-link title="GRANTS"
                                        icon="fas fa-solid fa-people-arrows"
                                        class="sh-blue-grey standalone-grants"
                                        :route="route('panel.manager.event.grants.index', $event->id)" />


                <li class="sh-blue-grey">
                    <x-mfw::nav-opening-header title="Comptabilité" icon="fas fa-eur" />
                    <ul class="nav child_menu">
                        <x-mfw::nav-link :route="route('panel.manager.event.orders.dashboard', $event->id)"
                                         title="Dashboard"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.orders.index', $event->id) }}"
                                         title="Commandes"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.invoice.index', $event->id)"
                                         title="Factures"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.refunds.index', $event->id)"
                                         title="Avoirs"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.payment.index', $event->id)"
                                         title="Paiements"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.orders.orators', $event->id)"
                                         title="Achats Intervenants"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.event_deposit.index', $event->id)"
                                         title="Cautions"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link :route="route('panel.manager.event.sage', $event->id)"
                                         title="SAGE"
                                         class="sh-blue-grey" />
                    </ul>
                </li>

                <li class="sh-blue-grey">
                    <x-mfw::nav-opening-header title="Programme" icon="fas fa-calendar" />
                    <ul class="nav child_menu">
                        <x-mfw::nav-link route="{{ route('panel.manager.event.program.organizer.index', $event->id) }}"
                                         title="Programme détaillé"
                                         class="sh-blue-grey" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.program.containers.index', $event->id) }}"
                                         title="Conteneurs"
                                         className="sh-blue-grey item-program-containers" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.program.session.index', $event->id) }}"
                                         title="Sessions"
                                         className="sh-blue-grey item-sessions" />
                        <x-mfw::nav-link route="{{ route('panel.manager.event.program.intervention.index', $event->id) }}"
                                         title="Interventions"
                                         className="sh-blue-grey item-interventions" />
                    </ul>
                </li>

                @include('nav.dev')

                <x-mfw::nav-header-link title="Quitter"
                                        icon="fas fa-power-off"
                                        :route="route('panel.dashboard')"
                                        class="sh-dark-grey" />


            </ul>
        </div>
    </div>
</nav>
