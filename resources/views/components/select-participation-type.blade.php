@props([
    'useDefault' => true,
    'defaultValue' => "",
    'defaultLabel' => "---",
    'event' => null,
    'selectedValue' => $attributes->get('value'),
    'excludeGroups' => [],
    'group' => null,
    'showGroups' => true,
])

@php
    use App\Accessors\ParticipationTypes;use App\Enum\ParticipantType;

    $locale = app()->getLocale();
    $participationTypes = ParticipationTypes::selectable($event);


    // Remove excluded groups
    if($excludeGroups){
        $participationTypes = array_diff_key($participationTypes, array_flip($excludeGroups));
    }

    // Check if the group is allowed
    if($group && !in_array($group, $excludeGroups)) {
        $groupName = match($group){
            'participant' => ParticipantType::CONGRESS->value,
            'speaker' => ParticipantType::ORATOR->value,
            default => $group,
        };

        if($showGroups){
            $participationTypes = [$groupName => $participationTypes[$groupName] ?? []]; // Avoid undefined key error
        }
        else{
            $participationTypes = $participationTypes[$groupName] ?? []; // Avoid undefined key error
        }
    } else {
        $showGroups = true;
    }

@endphp
<select {{ $attributes->except('value') }}>
    @if($useDefault)
        <option value="{{ $defaultValue }}" @if($selectedValue == $defaultValue) selected @endif>
            {{ $defaultLabel }}
        </option>
    @endif
    @if($showGroups)
        @foreach($participationTypes as $groupKey => $items)
            <optgroup label="{{ ParticipantType::translated($groupKey) }}">
                @foreach($items as $participationType)
                    <option value="{{ $participationType['id'] }}"
                            @if($selectedValue == $participationType['id']) selected @endif>
                        {{ $participationType['name'][$locale] }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    @else
        @foreach($participationTypes as $participationType)
            <option value="{{ $participationType['id'] }}"
                    @if($selectedValue == $participationType['id']) selected @endif>
                {{ $participationType['name'][$locale] }}
            </option>
        @endforeach
    @endif
</select>
