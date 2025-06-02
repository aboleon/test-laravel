{{-- Wrap in <div class="counter d-flex align-items-center"> --}}
@props([
    'count',
    'label'
])
<div class="text-center me-3 rounded py-1 px-3 border border-dark-subtle fw-bold">
                    <span style="font-size: 40px;color: var(--ab-blue-grey);"
                          class="d-block">{{ $count }}</span>
    <small class="d-block" style="margin-top: -10px">{{ $label }}</small>
</div>
