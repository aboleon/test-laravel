@php
    $id = rtrim(str_replace(['[',']'],'_', $name),'_');
@endphp
@if ($label)
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
@endif

<select id="{{ $id }}"
        @if (!$disablename)
            name="{{ $name }}"
        @endif
        class="form-control" title="{{ $label ?: $name }}">
    @if ($nullable)
        <option value="">{{ $defaultselecttext }}</option>
    @endif

    @foreach($values as $key => $value)
        <optgroup label="{{$value['name']}}">
            @foreach($value['relations'] as $k => $v)
                <option value="{{ $k }}"{{ $affected && $k == $affected ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </optgroup>
    @endforeach
</select>
