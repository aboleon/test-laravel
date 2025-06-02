@props(['keys'])

@php
    $errorKeys = is_array($keys) ? $keys : [$keys];
@endphp

<div {{ $attributes }}>
    @foreach($errorKeys as $key)
        @if($errors->has($key))
            <div class="alert alert-danger fs-14">
                @foreach($errors->get($key) as $error)
                    {!! $error !!}<br>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
