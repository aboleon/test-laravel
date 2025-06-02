<div
        wire:ignore.self
        class="modal fade"
        id="livewire_email_modal"
        tabindex="-1"
        aria-labelledby="livewire_email_modal_desc"
        aria-hidden="true">
    <div class="modal-dialog" wire:keydown.enter.prevent="saveEmail">
        <div class="modal-form">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h1 class="modal-title fs-5"
                        id="livewire_email_modal_desc" x-text="modalEmailTitle">
                    </h1>
                    <x-front.livewire-ajax-spinner target="loadEmail, saveEmail, deleteEmail" />
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">

                    @error('saveEmailException')
                    <div class="alert alert-danger my-3">{{ $message }}</div>
                    @enderror


                    <div class="mb-3">
                        <label for="modal_email"
                               class="form-label">{{__('front/account.labels.email')}}</label>
                        <input type="email"
                               class="form-control"
                               id="modal_email"
                               wire:model="email"
                        >
                        @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary action-cancel"
                            data-bs-dismiss="modal">
                        {{__('front/ui.close')}}
                    </button>
                    <button type="button"
                            wire:click="saveEmail"
                            class="btn btn-primary">{{__('front/ui.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@pushonce('js')
    <script>
      Livewire.on('emailSaved', () => {
        $('#livewire_email_modal').modal('hide');
      });
    </script>
@endpushonce


