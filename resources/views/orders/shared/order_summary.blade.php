@if ($orderAccessor->isOrder())
    <div class="text-center">
        <h5 class="d-block fw-bold fs-5 text-dark mfw-line-separator py-3 my-3">Montant payant de la commande</h5>
        <div class="fw-bold fs-4 text-dark pb-3 "><span class="order-totals">{{ $order->total_net + $order->total_vat }} €</span>
        </div>
        <div class="fw-bold fs-6 text-dark mb-3">HT: <span
                class="order-totals-ht">{{ round($order->total_net,2) }}</span> - TVA: <span
                class="order-totals-vat">{{  round($order->total_vat,2) }}</span> - PEC: <span
                class="order-totals-pec">{{  round($order->total_pec,2) }}</span></div>
        <div class="invoiced {{ $invoiced ? 'd-none' : '' }}">
            <div class="mfw-line-separator mb-4"></div>
            <button id="make-order-and-redirect" type="submit" class="btn btn-info text-white mx-2">Éditer et sortir</button>
            <button id="make-order" type="submit" class="btn btn-success">Éditer et rester</button>
        </div>
    </div>
@else
    <div class="text-center">
        <button id="make-order-and-redirect" type="submit" class="btn btn-info text-white mx-2" {{ $errors->any() ? '' : 'disabled' }}>Créer et sortir</button>
        <button id="make-order" type="submit" class="btn btn-success" {{ $errors->any() ? '' : 'disabled' }}>Créer et rester
        </button>
    </div>
@endif
