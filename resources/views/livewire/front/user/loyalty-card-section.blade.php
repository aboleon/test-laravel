@php use App\Accessors\Dates; @endphp
<div x-data="{modalTitle: ''}">
    <div class="card border mt-4">
        <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-light-emphasis">{!! __('front/account.loyalty_cards') !!}</h5>
            <a href="#"
               class="gui-icon"
               data-bs-toggle="modal"
               data-bs-target="#livewire_loyalty_card_modal"
               x-on:click='$wire.resetLoyaltyCard(); $wire.id=0; modalTitle = "{{__('front/account.add_a_loyalty_card')}}"'
               title="{{__('front/ui.add')}}"><i class="bi bi-plus-circle-fill"></i></a>
        </div>
        <div class="card-body">
            <ol class="list-group" id="loyalty-card-container">
                @foreach($account->cards as $card)
                    <li class="list-group-item" wire:key="{{ $card->id }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-bold text-name">{{$card->name}}</div>
                            <div class="badge bg-tint ms-auto text-serial">{{$card->serial}}</div>
                        </div>

                        <div class="mt-2">
                            @if($card->expires_at)
                                <small><b>{{__('front/account.expires_on')}}
                                        :</b>
                                    <span class="text-expires_at">
                                        {{$card->expires_at?->format(Dates::getFrontDateFormat())}}
                                </span>
                                </small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end gap-1">
                            <a href="#"
                               class="gui-icon gui-action"
                               data-bs-toggle="modal"
                               data-bs-target="#livewire_loyalty_card_modal"
                               x-on:click='modalTitle = "{{__("front/account.update_a_loyalty_card")}}"'
                               wire:click="load({{$card->id}})"
                               title="{{__("front/ui.edit")}}"><i class="bi bi-pencil-square"></i></a>
                            <a href="#"
                               class="gui-icon"
                               data-bs-toggle="modal"
                               data-bs-target="#confirm_modal"
                               @click.prevent="$store.confirm_modal.confirm = () => function(){ $wire.delete({{$card->id}}); }"
                               title="{{__("front/ui.delete")}}"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
    @include('front.user.account.modal.livewire_loyalty_card_modal')
</div>


@push('js')
    <script>
      Livewire.on('loyaltyCardDeleted', () => {
        Alpine.store('confirm_modal').close();
      });
      Livewire.on('deleteError', (errMsg) => {
        Alpine.store('confirm_modal').error(errMsg);
      });
    </script>
@endpush
