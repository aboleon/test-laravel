@props(['errors'])


@if ($errors->any())
    <div {{ $attributes }}>
        <div class="alert alert-danger" role="alert">
            {{ __('front/ui.oops_some_error') }}
            <ul class="text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>


    </div>
@endif


