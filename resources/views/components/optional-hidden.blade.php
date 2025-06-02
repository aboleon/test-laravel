@props([
    'string'
])
@if (request()->filled($string))
    <input type="hidden" name="{{ $string }}" value="{{ request($string) }}"/>
@endif
