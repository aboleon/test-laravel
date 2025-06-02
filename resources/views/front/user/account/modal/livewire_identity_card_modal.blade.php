@php use App\Accessors\Dates; @endphp
<div
        wire:ignore.self
        class="modal fade"
        id="livewire_identity_card_modal"
        tabindex="-1"
        aria-labelledby="livewire_identity_card_modal_desc"
        aria-hidden="true">
    <div class="modal-dialog" wire:keydown.enter.prevent="save">
        <div class="modal-form">
            <div class="modal-content" >
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h1 class="modal-title fs-5"
                        id="livewire_identity_card_modal_desc" x-text="modalTitle">
                    </h1>
                    <x-front.livewire-ajax-spinner target="save, load" />
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">


                    @error('saveException')
                    <div class="alert alert-danger my-3">{!! nl2br($message) !!}</div>
                    @enderror



                    <div>

                        <input type="hidden" wire:model="id">


                        <div class="mb-3">
                            <label for="modal_icard_input_name"
                                   class="form-label">{{__('front/account.labels.name')}}</label>
                            <input type="text"
                                   autocomplete="name"
                                   class="form-control"
                                   id="modal_icard_input_name"
                                   wire:model="name"
                            >
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="modal_icard_input_serial"
                                   class="form-label">{{__('front/account.labels.serial')}}</label>
                            <input type="text"
                                   autocomplete="name"
                                   class="form-control"
                                   id="modal_icard_input_serial"
                                   wire:model="serial"
                            >
                            @error('serial')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="modal_icard_input_issued_on"
                                   class="form-label">{{__('front/account.labels.issued_on')}}</label>
                            <input type="text"
                                   autocomplete="bday"
                                   class="form-control"
                                   id="modal_icard_input_issued_on"
                                   x-mask="{{Dates::getFrontDateFormat("x-mask")}}"
                                   placeholder="{{Dates::getFrontDateFormat("placeholder")}}"
                                   wire:model.debounce.1ms="emitted_at"
                            >
                            @error('emitted_at')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="modal_icard_input_expires_on"
                                   class="form-label">{{__('front/account.labels.expires_on')}}</label>
                            <input type="text"
                                   autocomplete="bday"
                                   class="form-control"
                                   id="modal_icard_input_expires_on"
                                   x-mask="{{Dates::getFrontDateFormat("x-mask")}}"
                                   placeholder="{{Dates::getFrontDateFormat("placeholder")}}"
                                   wire:model.debounce.1ms="expires_at"
                            >
                            @error('expires_at')
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

@pushonce('js')
    <script>
      Livewire.on('identityCardSaved', () => {
        $('#livewire_identity_card_modal').modal('hide');
      });
    </script>
@endpushonce

