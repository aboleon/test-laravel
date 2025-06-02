@props([
    'callback'
])

@push("js")
    <script src="{{asset("js/GoogleMapApiInit.js")}}"></script>
    <script>
      window.GoogleMapApiHelper.addCallback("{{ $callback }}");
    </script>

@endpush



@pushonce("js")
    <script src="{{asset("/js/GoogleMapHelper.js")}}"></script>
@endpushonce

@pushonce('js_last')
    <script async defer src="{{asset("js/GoogleMapApiTrigger.js")}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('mfw-api.google.places') }}&libraries=places&callback=onGoogleMapsApiReady"
            async
            defer></script>
@endpushonce


@push('css')
    <style>
        .pac-container {
            z-index: 1000000;
        }
    </style>
@endpush
