@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'selectedValue' => $attributes->get('value'),
])

@php
    use App\Accessors\Establishments;use App\Accessors\ParticipationTypes;
    use App\Enum\ParticipantType;
    $locale = app()->getLocale();
    $items = Establishments::orderedIdNameArray();
@endphp

<select {{ $attributes->except('value') }}>
    @if($useDefault)
        <option value="{{ $defaultValue }}" @if($selectedValue == $defaultValue) selected @endif>
            {{ $defaultLabel }}
        </option>
    @endif
        @foreach($items as $value => $label)
            <option value="{{$value}}" @if($selectedValue == $value) selected @endif>{{$label}}</option>
        @endforeach
</select>
