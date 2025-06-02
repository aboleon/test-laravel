@php
    use App\Accessors\Accounts;
    $billingAddressId = Accounts::getBillingAddressByAccount($account)?->id;
    $user = $account?->user;
    $mainEmail = old("email", $account?->email);
    $this->mainEmail = (string)$mainEmail;


@endphp
<div x-data="{
    modalEmailTitle: '',
    }">
    <div class="card border mt-4">
        <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-light-emphasis">{{__('front/account.mails_title')}}</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="row">
                        <label for="input_main_email"
                               class="col-md-4 col-form-label text-start">{{__('front/account.main_mail')}}<span class="text-danger">*</span>
                        </label>
                        <div class="col-md-8">
                            <input type="email"
                                   wire:model="mainEmail"
                                   @keydown.enter="$wire.saveMainEmail()"
                                   name="email"
                                   class="form-control rounded-0"
                                   id="input_main_email">
                        </div>
                    </div>
                    <div class="row container mt-2">
                        <x-front.specific-form-errors
                                :keys="[
                        'saveMainEmailException',
                        'saveMainEmailValidation',
                        'makeEmailDefault',
                        ]" />

                        <x-front.session-notifs prefix="mail." />

                    </div>
                </div>

                <div class="col mt-3 mt-md-0">
                    <div class="col-form-label d-flex gap-2 align-items-center">
                        {!! __('front/account.other_emails') !!}
                        <div>
                            <a href="#"
                               class="gui-icon"
                               data-bs-toggle="modal"
                               data-bs-target="#livewire_email_modal"
                               x-on:click='$wire.id=0; $wire.email=""; modalEmailTitle = "{{__('front/account.add_an_email')}}"'
                            ><i class="bi bi-plus-circle-fill"></i></a>
                            <x-front.livewire-ajax-spinner space="ms-1" target="makeEmailDefault" />
                        </div>
                    </div>
                    <ul class="list-group mt-2" id="email-container">
                        @foreach($account->mails as $mail)
                            <li class="list-group-item d-flex" wire:key="{{$mail->id}}">
                                <span class="text">{{$mail->email}}</span>
                                <div class="actions d-flex gap-1 ms-auto"
                                >
                                    <a href="#"
                                       class="gui-icon"
                                       wire:click.prevent="makeEmailDefault({{$mail->id}})"
                                       title="{{__("front/ui.set_as_default")}}"><i
                                                class="bi bi-star"></i></a>
                                    <a href="#"
                                       class="gui-icon"
                                       data-bs-toggle="modal"
                                       data-bs-target="#livewire_email_modal"
                                       x-on:click='modalEmailTitle = "{{__("front/user/account.update_an_email")}}"'
                                       wire:click="loadEmail({{$mail->id}})"
                                       title="{{__("front/ui.edit")}}"><i class="bi bi-pencil-square"></i></a>
                                    <a href="#"
                                       class="gui-icon"
                                       data-bs-toggle="modal"
                                       data-bs-target="#confirm_modal"
                                       @click.prevent="$store.confirm_modal.confirm = () => function(){ $wire.deleteEmail({{$mail->id}}); }"
                                       title="{{__("front/ui.delete")}}"><i class="bi bi-x-circle"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>


            @if($this->showSubmitButton)
                <button @click="$wire.saveMainEmail()"
                        class="btn btn-primary btn-sm">{{__("front/ui.validate")}}</button>
            @endif

        </div>
    </div>


    @include('front.user.account.modal.livewire_email_modal')

    @push('js')
        <script>

          Livewire.on('emailDeleted', () => {
            Alpine.store('confirm_modal').close();
          });

          Livewire.on('deleteError', (errMsg) => {
            Alpine.store('confirm_modal').error(errMsg);
          });
        </script>
    @endpush

</div>
