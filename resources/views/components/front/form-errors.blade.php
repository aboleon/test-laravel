@if($errors->any())
    <div class="alert alert-danger fs-14">
        <h4 class="alert-heading d-flex gap-2">
            <i class="bi bi-exclamation-triangle"></i> {{ __('front/ui.warning')}}
        </h4>
        <p class="mb-2">{{ __('front/ui.please_fix_following_errors')}}</p>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{!! $error !!}</li>
            @endforeach
        </ul>
    </div>
@endif
