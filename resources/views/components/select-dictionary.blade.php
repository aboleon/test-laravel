@props([
    'key',
    'useDefault' => true,
    'alphaSort' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'selectedValue' => $attributes->get('value'),
])
@php use App\Accessors\Dictionnaries; @endphp
@php
    $items = Dictionnaries::selectValues($key, [
        "alphaSort" => $alphaSort,
]);
@endphp
<select {{$attributes->except('value')}}>
    @if($useDefault)
        <option value="{{$defaultValue}}" @if($selectedValue == $defaultValue) selected @endif>{{$defaultLabel}}</option>
    @endif
    @foreach($items as $value => $label)
        <option value="{{$value}}" @if($selectedValue == $value) selected @endif>{{$label}}</option>
    @endforeach
</select>