<nav class="row justify-content-between align-items-center" id="event-nav-tab">
    <div class="col-md-9">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <x-mfw::tab tag="order-tabpane" label="Commande" :active="true"/>
            @if ($orderAccessor->isOrder() && $orderAccessor->hasItems() && !$orderAccessor->isOrator())
                <x-mfw::tab tag="payments-tabpane" label="Paiements"/>
                <x-mfw::tab tag="refunds-tabpane" label="Avoirs"/>
            @endif
            <x-mfw::tab tag="notes-tabpane" label="Notes"/>
        </div>
    </div>
    <div class="col-md-3">
        @if($orderAccessor->isOrder())
            <div class="text-end rounded p-3 text-dark">
                <a href="{{ $dashboard_link }}" class="text-decoration-none">
                    <b>{{ $orderAccessor->attributedTo()['name'] }}</b></a>@if($orderAccessor->isNotGroup() && $event_contact)
                    {{ ' - ' . $event_contact['type'] }}
                    @if($event_contact['pec_authorized'])
                        <x-pec-mark/>
                    @endif
                @endif
            </div>
        @endif
    </div>
</nav>
