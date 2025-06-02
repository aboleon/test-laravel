<div class="modal fade" id="cgvModal" tabindex="-1" aria-labelledby="cgvModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cgvModalLabel">{{__('front/event.modal_cgv')}}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body text-start">
                <p>
                    {!!nl2br($event->texts->cancelation)!!}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{__('front/event.modal_cgv_ok')}}</button>
            </div>
        </div>
    </div>
</div>
