@php @endphp
@pushonce('js')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
    @pushonce('js')
        <script>
            let iti;

            function initIntlTelInput() {
                const input = document.querySelector("#phone_input");
                if (!input) return;

                if (window.iti && window.iti.destroy) {
                    window.iti.destroy();
                }

                iti = window.intlTelInput(input, {
                    loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
                    initialCountry: "fr",
                    countryOrder: ["fr", "be", "ch", "lu", "de", "es", "it"],
                    i18n: {
                        fr: "France",
                        be: "Belgique",
                        de: "Allemagne",
                        ch: "Suisse",
                        es: "Espagne"
                    }
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                initIntlTelInput();

                // Set phone values just before saving
                document.querySelector('[wire\\:click="savePhone"]').addEventListener("click", function () {
                    const phoneFullInput = document.querySelector("#hidden_phone");
                    const countryCodeInput = document.querySelector("#hidden_country_code");

                    if (iti) {
                        phoneFullInput.value = iti.getNumber();
                        countryCodeInput.value = iti.getSelectedCountryData().iso2.toUpperCase();

                        phoneFullInput.dispatchEvent(new Event('input'));
                        countryCodeInput.dispatchEvent(new Event('input'));
                    }
                });
            });

            // Called from Livewire when editing
            Livewire.on('setPhoneInput', (nationalPhone, countryCode) => {
                setTimeout(() => {
                    initIntlTelInput();
                    if (iti) {
                        iti.setCountry(countryCode.toLowerCase());
                        iti.setNumber(nationalPhone);
                    }
                }, 50);
            });

            Livewire.on('phoneSaved', () => {
                $('#livewire_phone_modal').modal('hide');
            });
            Livewire.on('resetPhoneInput', () => {
                setTimeout(() => {
                    initIntlTelInput();
                }, 50);
            });

            $('#livewire_phone_modal').on('hidden.bs.modal', function () {
                Livewire.emit('resetForm');
            });
        </script>


        <style>
            .iti {
                display: block;
            }
        </style>
    @endpushonce


    <style>
        .iti {
            display: block;
        }
    </style>
@endpushonce
<div
        wire:ignore.self
        class="modal fade"
        id="livewire_phone_modal"
        tabindex="-1"
        aria-labelledby="livewire_phone_modal_desc"
        aria-hidden="true">
    <div class="modal-dialog" wire:keydown.enter.prevent="savePhone">
        <div class="modal-form">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h1 class="modal-title fs-5"
                        id="livewire_phone_modal_desc" x-text="modalPhoneTitle">
                    </h1>
                    <x-front.livewire-ajax-spinner target="loadPhone, savePhone"/>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">

                    @error('savePhoneException')
                    <div class="alert alert-danger my-3">{{ $message }}</div>
                    @enderror

                    <div class="mb-3">
                        <label for="input_modal_phone_name"
                               class="form-label">{{__('front/account.name')}}</label>
                        <input type="text"
                               wire:model="name"
                               class="form-control"
                               id="input_modal_phone_name"
                               placeholder="{{__('front/account.name_placeholder')}}"
                        >
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-mfw::input name="phone_input"
                                  :params="['wire:model' => 'phone_input']"
                                  :label="__('front/account.phone_placeholder')"/>

                    <input type="hidden" wire:model="phone" id="hidden_phone">
                    <input type="hidden" wire:model="country_code" id="hidden_country_code">

                    @error('phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   wire:model="default"
                                   value="1"
                                   id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                {{__('front/account.num_principal')}}
                            </label>
                        </div>
                    </div>

                </div>


                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary action-cancel"
                            data-bs-dismiss="modal">
                        {{__('front/ui.close')}}
                    </button>
                    <button type="button"
                            wire:click="savePhone"
                            class="btn btn-primary">{{__('front/ui.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
