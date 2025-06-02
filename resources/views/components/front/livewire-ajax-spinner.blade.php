@props([
    'target' => '',
    'space' => 'ms-2',
    ])
<div
        wire:loading
        @if($target)
            wire:target="{{$target}}"
        @endif
        {{$attributes->merge([
       'class' => "spinner-border spinner-border-sm ajax-spinner $space",
       ])}}  role="status">
    <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
</div>
