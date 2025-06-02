@props([
    'label' => '',
    'enabled' => null
])

<span class="ms-2 fw-bold fs-6"><i class="bi {{ (bool)$enabled ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger' }}"></i> {{ $label }}</span>
