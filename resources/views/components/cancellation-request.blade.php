@if ($cart->cancellation_request)
    <div class="cancellation_requests mt-2">
        @if ($cart->getCancellations()->isNotEmpty())
            @foreach ($cart->getCancellations()->filter(fn($item) => is_null($item->cancelled_at)) as $cancelled)
                <span class="d-block text-dark fw-bold"
                      style="font-size: 12px;">{{ $cancelled->requested_at->format('d/m/Y à H:i') }} - demande d'annulation de {{ $cancelled->quantity }} ch.</span>
            @endforeach
        @else
            <span class="d-block text-danger fw-bold"
                  style="font-size: 12px;">{{ $cart->cancellation_request->format('d/m/Y à H:i') }} - demande d'annulation {{ $cart->cancelled_qty ? ' de ' . $cart->cancelled_qty .' ch.' : ''}} </span>
        @endif
    </div>
@endif
