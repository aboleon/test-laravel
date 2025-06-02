@php
    use App\Accessors\Accounts;use App\Helpers\PhoneHelper;
    $billingAddressId = Accounts::getBillingAddressByAccount($account)?->id;
    $user = $account->user;
    $phones = $account->phones;

@endphp
<div x-data="{modalPhoneTitle: ''}">
    <div class="card border mt-4">
        <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-light-emphasis">{{__('front/account.phones_title')}}</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">


                <div class="col mt-3 mt-md-0">
                    <div class="col-form-label d-flex gap-2 align-items-center">
                        {{ __('front/account.add_a_phone') }}
                        <a href="#"
                           class="gui-icon"
                           data-bs-toggle="modal"
                           data-bs-target="#livewire_phone_modal"
                           x-on:click='$wire.resetForm(); $wire.id=0; modalPhoneTitle = "{{__('front/account.add_a_phone')}}"'
                        ><i class="bi bi-plus-circle-fill"></i></a>

                        <span class="fw-bold smaller">{{__('front/account.phone_legend')}}</span>

                        <x-front.livewire-ajax-spinner space="ms-1"
                                                       target="makePhoneDefault" />
                    </div>
                    <ul class="list-group mt-2" id="phone-container">
                        @foreach($phones as $phone)
                            @php
                                $phoneNumber = PhoneHelper::getPhoneNumberByPhoneModel($phone);
                            @endphp

                            <li @class([
                              "list-group-item d-flex gap-2 align-items-start" => true,
                              "bg-primary-subtle border-dark-subtle" => $phone->default,
                                ])
                                wire:key="{{$phone->id}}">
                                <span class="mt-1 iti__flag iti__{{ strtolower($phone->country_code)}}"></span>
                                <span class="text-phone">{{$phoneNumber->formatNational()}}</span>
                                -
                                <span class="text-name">{{$phone->name}}</span>
                                <div class="actions d-flex gap-1 ms-auto">

                                    <a href="#"
                                       class="gui-icon"
                                       wire:click.prevent="makePhoneDefault({{$phone->id}})"
                                       title="{{__("front/ui.set_as_default")}}"><i
                                                class="bi bi-star"></i></a>
                                    <a href="#"
                                       class="gui-icon"
                                       data-bs-toggle="modal"
                                       data-bs-target="#livewire_phone_modal"
                                       x-on:click='modalPhoneTitle = "{{__("front/account.update_a_phone")}}"'
                                       wire:click="loadPhone({{$phone->id}})"
                                       title="{{__("front/ui.edit")}}"><i class="bi bi-pencil-square"></i></a>
                                    <a href="#"
                                       class="gui-icon"
                                       data-bs-toggle="modal"
                                       data-bs-target="#confirm_modal"
                                       @click.prevent="$store.confirm_modal.confirm = () => function(){ $wire.deletePhone({{$phone->id}}); }"
                                       title="{{__("front/ui.delete")}}"><i class="bi bi-x-circle"></i></a>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            @if($this->showSubmitButton)
                <button @click="$wire.saveMainPhone()"
                        class="btn btn-primary btn-sm">{{__("front/ui.validate")}}</button>
            @endif
        </div>
    </div>


    @include('front.user.account.modal.livewire_phone_modal')

    @push('js')
        <script>
          Livewire.on('phoneDeleted', () => {
            Alpine.store('confirm_modal').close();
          });

          Livewire.on('deleteError', (errMsg) => {
            Alpine.store('confirm_modal').error(errMsg);
          });
        </script>
    @endpush
</div>
