@php use App\Helpers\Front\FrontLocaleHelper; @endphp
@props([
    'id' => 'flatpickr-123',
    'locale' => FrontLocaleHelper::getLocale(),
    'config' => [],
])

@php
    $dateFormat = match($locale){
        'en' => 'Y-m-d',
        default => config('app.date_display_format'),
    };


@endphp

<input id="{{$id}}" type="text" {{$attributes->except(['id', 'locale', 'config'])}}>

@pushonce('css')
    <link rel="stylesheet" href="{!! asset('vendor/mfw/flatpickr/flatpickr.min.css') !!}" />
    <link rel="stylesheet"
          type="text/css"
          href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
@endpushonce

@pushonce('js')
    <script src="{!! asset('vendor/mfw/flatpickr/flatpickr.js') !!}"></script>
    <script src="{!! asset('vendor/mfw/flatpickr/locale/'. $locale .'.js') !!}"></script>
@endpushonce




@push('js')
    <script>
      (function(){
        let defaultConfig = {
          enableTime: false,
          dateFormat: '{{$dateFormat}}',
          time_24hr: true,
          locale: "{{ $locale }}",
        };

        let config = Object.assign(defaultConfig, @json($config));
        flatpickr('#{{$id}}', config);
      })()


    </script>
@endpush
