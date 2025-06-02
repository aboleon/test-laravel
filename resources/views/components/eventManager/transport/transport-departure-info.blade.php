@php
    $firstColClass = "w-200px"
@endphp
<table class="table-first-col-special table table-sm table-bordered">
    <tr>
        <td class="{{$firstColClass}}">{{ __('transport.departure_start_location') }}</td>
        <td>
            <b>{{$transport->departure_start_location}}</b> -
            {{$transport->departure_start_date?->format(config('app.date_display_format'))}}
            {{$transport->departure_start_time?->format('H:i')}}
        </td>
    </tr>
    <tr>
        <td class="{{$firstColClass}}">{{ __('transport.departure_end_location') }}</td>
        <td>
            <b>{{$transport->departure_end_location}}</b> -
            {{$transport->departure_end_time?->format('H:i')}}
        </td>
    </tr>
    <tr>
        <td class="{{$firstColClass}}">{{ __('front/transport.labels.departure_transport_type') }}</td>
        <td>
            {{$transport->departureTransportType?->name}}
        </td>
    </tr>
    <tr>
        <td class="{{$firstColClass}}">{{ __('ui.comment') }}</td>
        <td>
            {{$transport?->departure_participant_comment}}
        </td>
    </tr>
    @if ($transport?->departure_reference_info_participant)

        <tr>
            <td class="{{$firstColClass}}">{{ __('transport.reference_info_participant') }}</td>
            <td>
                {{$transport?->departure_reference_info_participant}}
            </td>
        </tr>
    @endif
</table>
