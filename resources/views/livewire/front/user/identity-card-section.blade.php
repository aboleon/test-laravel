@php use App\Accessors\Dates; @endphp
<div x-data="{modalTitle: ''}">
    <div class="card border mt-4">

        <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-light-emphasis">{!! __('front/account.identity_cards') !!}</h5>
            <a href="#"
               class="gui-icon"
               data-bs-toggle="modal"
               data-bs-target="#livewire_identity_card_modal"
               x-on:click='$wire.resetIdentityCard(); $wire.id=0; modalTitle = "{{__("front/account.add_an_identity_card")}}"'
               title="{{__('front/ui.add')}}"><i class="bi bi-plus-circle-fill"></i></a>
        </div>
        <div class="card-body">
            <ol class="list-group" id="identity-card-container">
                @foreach($account->documents as $document)
                    <li class="list-group-item"
                        wire:key="{{ $document->id }}">
                        <div class="d-flex flex-column justify-content-start align-items-start">
                            <div class="fw-bold text-name">{{$document->name}}</div>
                            <small class="text-serial text-danger-emphasis">{{$document->serial}}</small>
                        </div>

                        <div class="mt-0">
                            <small><b>{{__('front/account.issued_on')}}
                                    :</b>
                                <span class="text-emitted_at">{{$document->emitted_at->format(Dates::getFrontDateFormat())}}</span>
                            </small><br>
                            <small><b>{{__('front/account.expires_on')}}
                                    :</b>
                                <span class="text-expires_at">{{$document->expires_at->format(Dates::getFrontDateFormat())}}</span>
                            </small>
                        </div>
                        <div class="d-flex justify-content-end gap-1">
                            <a href="#"
                               class="gui-icon"
                               data-bs-toggle="modal"
                               data-bs-target="#livewire_identity_card_modal"
                               x-on:click='modalTitle = "{{__("front/account.update_an_identity_card")}}"'
                               wire:click="load({{$document->id}})"
                               title="{{__("front/ui.edit")}}"><i class="bi bi-pencil-square"></i></a>
                            <a href="#"
                               class="gui-icon"
                               data-bs-toggle="modal"
                               data-bs-target="#confirm_modal"
                               @click.prevent="$store.confirm_modal.confirm = () => function(){ $wire.delete({{$document->id}}); }"


                               title="{{__("front/ui.delete")}}"><i class="bi bi-x-circle"></i></a>


                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    @include('front.user.account.modal.livewire_identity_card_modal')
</div>


@push('js')
    <script>
      Livewire.on('identityCardDeleted', () => {
        Alpine.store('confirm_modal').close();
      });
      Livewire.on('deleteError', (errMsg) => {
        Alpine.store('confirm_modal').error(errMsg);
      });
    </script>
@endpush
