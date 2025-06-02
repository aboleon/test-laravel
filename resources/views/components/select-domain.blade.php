@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'event' => null,
    'selectedValue' => $attributes->get('value'),
])
@php use App\Accessors\Dictionnaries; @endphp
@php
    $items = Dictionnaries::selectValues("domain");
    if($event){
        $domainsIds = array_column($event->domains->toArray(), 'id');
        $items = array_filter($items, function($key) use ($domainsIds){
            return in_array($key, $domainsIds);
        }, ARRAY_FILTER_USE_KEY);

    }
@endphp
<select {{$attributes->except('value')}}>
    @if($useDefault)
        <option value="{{$defaultValue}}" @if($selectedValue == $defaultValue) selected @endif>{{$defaultLabel}}</option>
    @endif
    @foreach($items as $value => $label)
        <option value="{{$value}}" @if($selectedValue == $value) selected @endif>{{$label}}</option>
    @endforeach
</select>