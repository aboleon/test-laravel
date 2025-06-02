@props([
    'width' => 20,
    'height' => 20,
    'type' => 'secondary',
])
<div class="d-inline-block rounded-circle bg-{{ $type }}"
     style="width: {{ $width }}px;height: {{ $height }}px;">
    &nbsp;
</div>
