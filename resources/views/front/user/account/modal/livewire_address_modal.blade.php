@php
    use App\Accessors\Countries;
    use App\Accessors\Dates;
@endphp
<div
        wire:ignore.self
        class="modal fade"
        id="livewire_address_modal"
        tabindex="-1"
        aria-labelledby="livewire_address_modal_desc"
        aria-hidden="true">
    <div class="modal-dialog" wire:keydown.enter.prevent="save">
        <div class="modal-form">
            <div class="modal-content"
                 x-data="{
                            street_number: '',
                            locality: '',
                            route: '',
                            postal_code: '',
                            country_code: '',
                            cedex: '',
                            updateTextAddress(){
                                let address = '';
                                if(this.$wire.street_number){
                                    address += this.$wire.street_number + ' ';
                                }
                                if(this.$wire.route){
                                    address += this.$wire.route + ', ';
                                }
                                if(this.$wire.postal_code){
                                    address += this.$wire.postal_code + ' ';
                                }
                                if(this.$wire.locality){
                                    address += this.$wire.locality;
                                }
                                if(this.$wire.country_code){
                                    address += ' - ' + this.$wire.country_code;
                                }
                                if(this.$wire.cedex){
                                    address += ' - ' + this.$wire.cedex;
                                }
                                this.$wire.set('text_address', address);
                            }
                        }"
            >
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h1 class="modal-title fs-5"
                        id="livewire_address_modal_desc" x-text="modalTitle"></h1>
                    <x-front.livewire-ajax-spinner target="save, load"/>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">

                    @error('saveException')
                    <div class="alert alert-danger my-3">{{ $message }}</div>
                    @enderror


                    <div>

                        <input type="hidden" wire:model="id">

                        <div class="mb-3">
                            <label for="modal_address_input_name"
                                   class="form-label">{{__('front/account.labels.name')}}</label>
                            <input type="text"
                                   autocomplete="name"
                                   class="form-control"
                                   id="modal_address_input_name"
                                   wire:model="name"
                            >
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="modal_address_input_company"
                                   class="form-label">{{__('front/account.labels.company')}}</label>
                            <input type="text"
                                   autocomplete="name"
                                   class="form-control"
                                   id="modal_address_input_company"
                                   wire:model="company"

                            >
                            @error('company')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <div class="form-check">
                                <input
                                        wire:model="billing"
                                        class="form-check-input"
                                        type="checkbox"
                                        value="1"
                                        id="modal_address_checkbox_billing">
                                <label class="form-check-label"
                                       for="modal_address_checkbox_billing">
                                    {{__('front/account.labels.billing')}}
                                </label>
                            </div>
                            @error('billing')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        <div
                                class="mb-3">
                            <div>
                                <label for="modal_address_input_text_address"
                                       class="form-label">{{__('front/account.labels.text_address')}}</label>
                                <input type="text"
                                       class="form-control border-2 border-success shadow"
                                       id="modal_address_input_text_address"
                                       wire:model="text_address"
                                >
                            </div>

                            <div class="row g-3 mt-2 p-3 bg-body-secondary">
                                <div class="col-md-2">
                                    <label for="input-street-number"
                                           class="form-label">{{__('front/account.labels.address_number')}}</label>
                                    <input type="text"
                                           id="input-street-number"
                                           class="form-control form-control-sm"
                                           @input="updateTextAddress()"
                                           wire:model="street_number"
                                           :disabled="$wire.street_number === ''">
                                </div>
                                <div class="col-md-10">
                                    <label for="input-route"
                                           class="form-label">{{__('front/account.labels.address_street')}}</label>
                                    <input class="form-control form-control-sm"
                                           type="text"
                                           wire:model="route"
                                           @input="updateTextAddress()"
                                           id="input-route"
                                           :disabled="$wire.route === ''">
                                </div>
                                <div class="col-md-6">
                                    <label for="input-postal_code" class="form-label">
                                        {{__('front/account.labels.address_zip_code')}}
                                    </label>
                                    <input class="form-control form-control-sm" type="text"
                                           wire:model="postal_code"
                                           @input="updateTextAddress()"
                                           id="input-postal_code"
                                           :disabled="$wire.postal_code === ''">
                                </div>
                                <div class="col-md-6">
                                    <label for="input-locality"
                                           class="form-label">{{__('front/account.labels.address_city')}}</label>
                                    <input class="form-control form-control-sm"
                                           type="text"
                                           id="input-locality"
                                           @input="updateTextAddress()"
                                           wire:model="locality"
                                           :disabled="$wire.locality === ''">
                                </div>
                                <div class="col-md-6">
                                    <label for="input-country_code"
                                           class="form-label">{{__('front/account.labels.address_country')}}</label>
                                    <select class="form-select form-select-sm"
                                            wire:model="country_code"
                                            @change="updateTextAddress()"
                                            id="input-country_code"
                                            :disabled="$wire.country_code === ''">
                                        @php
                                            $codes = Countries::getCode2Name();
                                        @endphp
                                        <option
                                            value="">{{__('front/account.labels.address_choose_a_country')}}</option>
                                        @foreach($codes as $code => $name)
                                            <option value="{{$code}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="input-cedex"
                                           class="form-label">{{__('front/account.labels.address_cedex')}}</label>
                                    <input class="form-control form-control-sm"
                                           type="text"
                                           @input="updateTextAddress()"
                                           id="input-cedex"
                                           wire:model="cedex"
                                           :disabled="$wire.cedex === ''">
                                </div>
                            </div>
                            <input type="hidden" wire:model="lat">
                            <input type="hidden" wire:model="lon">


                            @error('text_address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="mb-3">
                            <label for="modal_address_text_complementary"
                                   class="form-label">{{__('front/account.labels.address_complement')}}</label>
                            <textarea
                                    class="form-control form-control-sm"
                                    id="modal_address_text_complementary"
                                    wire:model="complementary"

                            ></textarea>
                            @error('complementary')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        {{__('front/ui.close')}}
                    </button>
                    <button type="button"
                            wire:click="save"
                            class="btn btn-primary btn-submit">{{__('front/ui.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>



@push('js')

    <script>
        let component;

        $(document).ready(function () {
            let jAddressSection = $('#addressSection');
            let livewireComponentId = jAddressSection.attr('wire:id');
            component = Livewire.find(livewireComponentId);
        });

        function gmapReadyForAccountAddress() {
            GoogleMapHelper.init('#modal_address_input_text_address', {
                change: function (address) {
                    component.set('text_address', address.text_address);
                    component.set('street_number', address.street_number);
                    component.set('route', address.route);
                    component.set('locality', address.locality);
                    component.set('country_code', address.country_code);
                    component.set('postal_code', address.postal_code);
                    component.set('lat', address.latitude);
                    component.set('lon', address.longitude);
                },
            });
        }

        Livewire.on('addressSaved', () => {
            $('#livewire_address_modal').modal('hide');

            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const params = new URLSearchParams(url.search);
            if (params.has('redirectToCart')) {
                setTimeout(function () {
                    window.location.assign($('#gotocart').attr('data-route'));
                }, 1000);
            }
        });

    </script>
@endpush

<x-google-map-helper callback="gmapReadyForAccountAddress"/>
