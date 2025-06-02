<div class="wg-card">
    <header class="mfw-line-separator mb-3">
            <span class="mfw-badge mfw-bg-red float-end"
                  style="padding: 5px 10px 3px 11px;">{{ $orders->count() }}</span>
        <h4>Commandes / Factures</h4>
    </header>


    <table class="table">
        @include('orders.shared.order-detail-thead')
        <tbody>
        @forelse($orders as $order)
            <x-order-row-detail :order="$order" :services="$services"/>
        @empty
        @endforelse
        </tbody>
    </table>

</div>
