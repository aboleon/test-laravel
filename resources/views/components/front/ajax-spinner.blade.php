<div {{$attributes->merge([
    'class' => "spinner-border spinner-border-sm ajax-spinner",
    'style' => "display: none",
    ])}}  role="status">
  <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
</div>
