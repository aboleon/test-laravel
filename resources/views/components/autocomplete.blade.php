@php
    /**
    * https://bootstrap-autocomplete.readthedocs.io/en/latest/
    */
@endphp
@props([
    'id' => 'autocomplete',
    'label' => null,
    'name' => 'autocomplete',
    'affected' => null,
    'validation_id' => '',
    'action' => null,
    'url' => '',
    'placeholder' => __('forms.type_to_search'),
    'extraQueryString' => '',
    'onSelect' => null,
    'minLength' => 2,
    'preventEnter' => true,

    //
    'default' => null, // or string: "value|text"
    ])

@php
    $validation_id = \MetaFramework\Functions\Helpers::generateValidationId($name);
@endphp

@if ($label)
    <label for="{{ $id }}" class="form-label">{!! $label !!}</label>
@endif

<select class="form-control"
        name="{{ $name }}"
        id="{{$id}}"
        value="10"
        placeholder="{{ $placeholder }}"
        autocomplete="off"></select>

<x-mfw::validation-error :field="$validation_id" />
@push('js')
    <script>

      document.addEventListener('DOMContentLoaded', function() {

        function debounce(func, wait) {
          let timeout;
          return function(...args) {
            const later = () => {
              clearTimeout(timeout);
              func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
          };
        }

        $('#{{$id}}').autoComplete({
          resolver: 'custom',
          preventEnter: {{$preventEnter?"true":"false"}},

          minLength: {{$minLength}},
          events: {
            search: debounce(function(qry, callback) {
              let formData = 'action={{$action}}&q=' + qry + "&{{$extraQueryString}}";
              ajax(formData, $('#{{$id}}'), {
                successHandler: function(result) {
                  callback(result.items);
                },
              });
            }, 300), // 300ms debounce
          },
        });

          @if ($onSelect)
          $('#{{$id}}').on('autocomplete.select', function(evt, item) {
              {{$onSelect}}(item);
          });
          @endif

          @if($default)
          @php
              list($defaultValue, $defaultText) = explode(':', $default, 2);
          @endphp
          $('#{{$id}}').autoComplete('set', {
            value: {!! json_encode($defaultValue) !!},
            text: {!! json_encode($defaultText) !!},
          });
          @endif
      });
    </script>
@endpush

@pushonce('js')
    {{--    <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.7/dist/latest/bootstrap-autocomplete.min.js"></script>--}}

    <!-- https://github.com/xcash/bootstrap-autocomplete/issues/108-->
    <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@xcash-v300/dist/latest/bootstrap-autocomplete.min.js"></script>
@endpushonce