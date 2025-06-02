@php
    use App\Accessors\Accounts;
    $billingAddressId = Accounts::getBillingAddressByAccount($account)?->id;
@endphp
<div x-data="{modalTitle: ''}" id="addressSection">
    <div class="card border mt-4">
        <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-light-emphasis">{{__('front/account.addresses')}}</h5>
            <div class="actions">
                <a href="#"
                   class="gui-icon"
                   data-bs-toggle="modal"
                   data-bs-target="#livewire_address_modal"
                   x-on:click='$wire.resetAddress(); $wire.id=0; modalTitle = "{{__('front/account.add_an_address')}}"'
                   title="{{__('front/ui.add')}}"><i class="bi bi-plus-circle-fill"></i></a>
            </div>
        </div>
        <div class="card-body">

            @if($account->address->isEmpty())
                <div class="alert alert-danger mt-3">
                    {{__('front/account.validation.at_least_one_address')}}
                </div>
            @endif

            <div id="address-container">
                @foreach($account->address as $address)
                    <div class="card border mb-3" wire:key="{{ $address->id }}">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div class="fw-bold">{{$address->name ?? "Adresse"}}</div>
                            @if($billingAddressId == $address->id)

                                <div class="badge bg-dark ms-auto">{{__('front/account.labels.billing_badge')}}</div>
                            @endif

                            <span class="d-none text-name">{{$address->name}}</span>
                            <span class="d-none text-billing">{{$address->billing ? "1":"0"}}</span>
                            <span class="d-none text-company">{{$address->company}}</span>
                        </div>
                        <div class="card-body">
                            <div>
                                <small>
                                    @if($address->company)
                                        <b>{{$address->company}}</b><br>
                                    @endif
                                    <span class="text-address">{{$address->text_address}}</span>
                                    <div class="text-secondary">{{$address->complementary}}</div>
                                </small>
                            </div>
                            <div class="d-flex justify-content-end gap-1">
                                <a href="#"
                                   class="gui-icon"
                                   data-bs-toggle="modal"
                                   data-bs-target="#livewire_address_modal"
                                   x-on:click='modalTitle = "{{__("front/account.update_an_address")}}"'
                                   wire:click="load({{$address->id}})"
                                   title="{{__("front/ui.edit")}}"><i class="bi bi-pencil-square"></i></a>
                                <a href="#"
                                   class="gui-icon"
                                   data-bs-toggle="modal"
                                   data-bs-target="#confirm_modal"
                                   @click.prevent="$store.confirm_modal.confirm = () => function(){ $wire.delete({{$address->id}}); }"
                                   title="{{__("front/ui.delete")}}"><i class="bi bi-x-circle"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


    </div>


    @include('front.user.account.modal.livewire_address_modal')

    @push('js')
        <script>
          Livewire.on('addressDeleted', () => {
            Alpine.store('confirm_modal').close();
          });

          Livewire.on('deleteError', (errMsg) => {
            Alpine.store('confirm_modal').error(errMsg);
          });
        </script>
    @endpush
</div>
