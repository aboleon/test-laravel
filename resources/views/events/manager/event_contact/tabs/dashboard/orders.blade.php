<div class="wg-card">
    <header class="mb-3">
            <span class="mfw-badge mfw-bg-red float-end"
                  style="padding: 5px 10px 3px 11px;">{{ $orders->count() }}</span>
        <h4>Commandes / Factures</h4>
    </header>
    <table class="table">
        <tbody>
        @forelse($orders as $order)
            <x-order-row-detail :eventContact="$eventContact" :order="$order" :services="$services"/>
        @empty
        @endforelse
        </tbody>
    </table>

    <div class="mfw-line-separator my-3"></div>

</div>
