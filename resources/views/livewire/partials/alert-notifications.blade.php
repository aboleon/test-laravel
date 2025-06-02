
@if($alertMessage)
    <div class="alert alert-{{ $alertMessageType }} alert-dismissable d-flex justify-content-between">
        {{ $alertMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('ui.close') }}"></button>
    </div>
@endif
