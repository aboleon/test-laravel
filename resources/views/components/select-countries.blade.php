@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'selectedValue' => $attributes->get('value'),
])
@php
    $countries = \MetaFramework\Accessors\Countries::orderedCodeNameArray();
@endphp
<select {{$attributes->except('value')}}>
    @if($useDefault)
        <option value="{{$defaultValue}}" @if($selectedValue == $defaultValue) selected @endif>{{$defaultLabel}}</option>
    @endif
    @foreach($countries as $value => $label)
        <option value="{{$value}}" @if($selectedValue == $value) selected @endif>{{$label}}</option>
    @endforeach
</select>