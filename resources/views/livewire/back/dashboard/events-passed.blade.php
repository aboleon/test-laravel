<div class="shadow p-4 bg-body-tertiary rounded">

    <div class="wg-card">
        <div class="row align-items-center">
            <div class="col-sm-6">
            <h4>Évènements passés -
                <span class="upcoming-events-count badge badge-sm btn-blue-gray">{{ $passedEvents->count() }}</span>
            </h4>
            </div>
            <div class="col-sm-4 text-end">
                <a class="btn btn-danger" href="{{ route('panel.dashboard') }}">
                    <i class="bi bi-chevron-double-left"></i>
                    Retour Tableau de bord
                </a>
            </div>
            <div class="col-sm-2 text-end">
                <x-front.livewire-ajax-spinner />
                <input wire:model.live.debounce.100ms="search"
                       type="search"
                       placeholder="Rechercher..."
                       class="form-control" />
            </div>
        </div>

        <div class="mfw-line-separator mb-4"></div>
    </div>

    <div class="row past-events-container">
        @forelse($passedEvents as $item)
            <x-eventManager.dashboard.dashboard-event-card container-class="col-sm-3" :families="$families" :event="$item" />
        @empty
            <div class="col-12">
                <x-mfw::alert type="info" message="Aucun évènement passé" />
            </div>
        @endforelse
    </div>
</div>
