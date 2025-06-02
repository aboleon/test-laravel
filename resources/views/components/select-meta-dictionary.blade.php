@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'key' => null,
    'selectedValue' => $attributes->get('value'),
    'whiteListIds' => null,
])
@php
    use App\Accessors\Dictionnaries;
    $items = Dictionnaries::selectValues($key);

    if ($whiteListIds) {
        foreach ($items as $key => &$group) {
            $group['values'] = array_intersect_key($group['values'], array_flip($whiteListIds));
            if (empty($group['values'])) {
                unset($items[$key]);
            }
        }
    }
@endphp

<select {{ $attributes->except('value') }}>
    @if($useDefault)
        <option value="{{ $defaultValue }}" @if($selectedValue == $defaultValue) selected @endif>
            {{ $defaultLabel }}
        </option>
    @endif
    @foreach($items as $info)
        <optgroup label="{{ $info['name'] }}">
            @foreach($info['values'] as $value => $label)
                <option value="{{ $value }}"
                        @if($selectedValue == $value) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
