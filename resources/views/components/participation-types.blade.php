@if($participations->isNotEmpty())
    <div class="participation_types">
        @if ($as == "select")
            <select name="{{ $name }}" class="form-control">
                @foreach($participations as $assigned_key => $assigned_group)
                    @php
                        $translated = \App\Enum\ParticipantType::translated($assigned_key);
                    @endphp
                    <optgroup label="{{ \App\Enum\ParticipantType::translated($assigned_key) }}">
                        @if ($all)
                            <option class="{{ $assigned_key }}" value="{{ $assigned_key }}"{{ $affected == $assigned_key ? ' selected' : '' }}>Tous ({{ $translated }})</option>
                        @endif
                        @foreach($assigned_group as $assigned)
                            <option class="{{ $assigned_key }}" value="{{ $assigned->id }}"{{ $affected == $assigned->id ? ' selected' : '' }}>{{ ($alltranslations ? implode(' / ', \App\Helpers\Translations::translationsState($assigned, 'name')) : $assigned->name) .' ('.$translated.')' }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

        @else
            @foreach($participations as $assigned_key => $assigned_group)
                <strong>{{ \App\Enum\ParticipantType::translated($assigned_key) }}</strong>
                @if ($all)
                    <x-mfw::checkbox name="{{ $name }}[]" :value="$assigned_key" :affected="$affected" label="Tous" class="ms-4 participation_all"/>
                @endif
                @foreach($assigned_group as $assigned)
                    @php
                        $filtered = $filter && !in_array($assigned->id, $subset) ? 'd-none' : '';
                    @endphp
                    <x-mfw::checkbox name="{{ $name }}[]" :value="$assigned->id" :affected="$affected" :label="$alltranslations ? implode(' / ', \App\Helpers\Translations::translationsState($assigned, 'name')) : $assigned->name" class="ms-4 parent-{{$assigned_key .' '.$filtered }}" :params="['data-parent' => $assigned_key]"/>
                @endforeach
            @endforeach
        @endif
    </div>
@endif
@pushonce('js')
    <script>
        $('div.participation_all :checkbox').off().click(function () {
            let val = $(this).val(),
                checked = $(this).is(':checked');
            if (checked) {
                $(this).closest('.participation_types').find(':checkbox[data-parent=' + val + ']').prop('checked', false);
            }
        });
        $('div.participation_types div[class*=parent-]').find(':checkbox').off().click(function () {
            if ($(this).is(':checked')) {
                $(this).closest('.participation_types').find('.participation_all :checkbox[value=' + $(this).attr('data-parent') + ']').prop('checked', false);
            }
        });
    </script>
@endpushonce
