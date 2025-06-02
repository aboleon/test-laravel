<div class="shadow p-4 bg-body-tertiary rounded">
    <div class="wg-card mb-4">
        <div class="d-flex justify-content-end mb-4">
            <div class="ml-auto d-flex align-items-center gap-2" id="topbar-actions">
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <x-front.livewire-ajax-spinner/>
                    <input wire:model.live.debounce.100ms="search"
                           type="search"
                           placeholder="Rechercher..."
                           class="form-control"/>
                </div>
                <div>
                    <a class="btn btn-sm btn-danger"
                       href="{{ route('panel.passed_events') }}">Evènements
                        passés</a>
                </div>
            </div>
        </div>

        <div class="row" id="container-events">
            <div class="col-md-6">
                <h4>Évènements à venir -
                    <span
                        class="upcoming-events-count badge badge-sm btn-blue-gray">{{ $upcomingEvents->count() }}</span>
                </h4>
                <div id="upcoming-events-container" class="row">
                    @forelse($upcomingEvents as $item)
                        <div class="col-12 col-lg-6">
                            <x-eventManager.dashboard.dashboard-event-card
                                :use-col="false"
                                :families="$families"
                                :event="$item"/>
                        </div>
                    @empty
                        <div class="col-12">
                            <x-mfw::alert type="info" message="Aucun évènement à vénir"/>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="col-md-6">
                <h4>Évènements passés de moins de 2 mois -
                    <span
                        class="past-events-count badge badge-sm btn-blue-gray">{{ $past2MonthsEvents->count() }}</span>
                </h4>
                <div id="past-events-container" class="row">
                    @forelse($past2MonthsEvents as $item)
                        <div class="col-12 col-lg-6">
                            <x-eventManager.dashboard.dashboard-event-card :families="$families"
                                                                           :event="$item"/>
                        </div>
                    @empty
                        <div class="col-12">
                            <x-mfw::alert type="info"
                                          message="Aucun évènement passé de moins de 2 mois"/>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
