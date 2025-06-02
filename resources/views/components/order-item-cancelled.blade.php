<div
    class="cancelled fw-bold {{ ($cart->cancelled_at or $cart->getCancellations()->isNotEmpty()) ? '' : 'd-none' }}"
    style="font-size: 12px;">
    @forelse ($cart->getCancellations() as $cancelled)
        <span class="d-block text-danger fw-bold"
              style="font-size: 12px;">{{ $cancelled->cancelled_at->format('d/m/Y à H:i') }} - annulé {{ $cancelled->quantity }} ch.</span>
    @empty
        @if($cart->cancelled_at)
            Annulée le <span class="cancelled_time">{{$cart->cancelled_at?->format("d/m/Y à H\hi")}}</span>
        @endif
    @endforelse
</div>
