<div class="modal fade"
     id="modal-rgpd"
     tabindex="-1"
     aria-labelledby="modal-rgpd-label"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-rgpd-label">
                    {{__('front/register.rgpd_title')}}
                </h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                {{__('front/register.rgpd_text')}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
            </div>
        </div>
    </div>
</div>
