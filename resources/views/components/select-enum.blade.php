@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'selectedValue' => $attributes->get('value'),
    'className' => null,
])
@php use App\Accessors\Dictionnaries;use App\Enum\Civility; @endphp
@php

    $items = [];
    if($className){
        $items = $className::toArray();
    }
@endphp
<select {{$attributes->except('value')}}>
    @if($useDefault)
        <option value="{{$defaultValue}}"
                @if($selectedValue == $defaultValue) selected @endif>{{$defaultLabel}}</option>
    @endif
    @foreach($items as $value => $label)
        <option value="{{$value}}" @if($selectedValue == $value) selected @endif>{{$label}}</option>
    @endforeach
</select>