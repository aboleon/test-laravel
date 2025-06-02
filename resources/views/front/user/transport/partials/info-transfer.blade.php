@php
    $transferRequested = false;
    if($transport){
        $transferRequested = $transport->transfer_requested;
    }
    $type = $transfer_type??'';

    $hasInfo = $transport->{'transfer_shuttle_time'.$type} || $transport->{'transfer_info'.$type};


@endphp

<style>
    .transfert-none:last-of-type {
        display: none
    }
</style>

@if($transferRequested)
    @if($hasInfo)
        <div class="mt-4">
            <h5>{{__('transport.transfer_info') .' '. __('front/transport.'.str_replace('_','',$type)) }}</h5>
            <table class="table table-sm table-bordered">
                @if ($transport->{'transfer_shuttle_time'.$type})
                    <tr>
                        <td class="w-150px"><i class="bi bi-alarm"></i>
                            {{__('front/transport.shuttle_time')}}
                        </td>
                        <td>{{$transport->{'transfer_shuttle_time'.$type}->format('H\hi')}}</td>

                    </tr>
                @endif
                @if ($transport->{'transfer_info'.$type})
                    <tr>
                        <td class="w-150px"><i class="bi bi-info-circle"></i> {{__('front/transport.info')}}</td>
                        <td>{{ $transport->{'transfer_info'.$type} }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @else
        <p class="mt-4 text-info transfert-none">
            <i class="bi bi-exclamation-circle"></i>
            {{__('front/transport.transfer_info_coming_soon')}}
        </p>
    @endif
@endif
