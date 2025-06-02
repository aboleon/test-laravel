<style>
    .table-first-col-special {
        td:first-child {
            /*font-weight: bold;*/
        }

    }
</style>
<div class="p-2 mt-3">
    <h5>{{ __('front/transport.step_1_info') }}</h5>
    <table class="table-first-col-special table table-sm table-bordered">
        <tr>
            <td>{{ __('front/transport.labels.passport_name') }}</td>
            <td>{{ $profile?->passport_last_name .' '. $profile?->passport_first_name }}</td>
        </tr>
        <tr>
            <td>{{ __('account.birth') }}</td>
            <td>{{ $profile?->birth?->format(config('app.date_display_format')) }}</td>
        </tr>
        <tr>
            <td>{{ __('front/transport.labels.travel_preferences') }}</td>
            <td>{{ $transport->travel_preferences }}</td>
        </tr>
    </table>

    <h5>{{ __('front/transport.step_2_departure') }}</h5>
    <x-eventManager.transport.transport-departure-info :transport="$transport"/>

    <h5>{{ __('front/transport.step_3_return') }}</h5>
    <x-eventManager.transport.transport-return-info :transport="$transport"/>


    @if($isTransferEligible)
        <h5>{{ __('front/transport.step_4_transfer') }}</h5>

        @if($transport->transfer_requested)
            <p class="fw-bold text-info"><i
                    class="bi bi-check-circle"></i> {{ __('front/transport.transfer_requested') }}</p>
        @else
            <p class="fw-bold text-danger"><i
                    class="bi bi-x-circle"></i> {{ __('front/transport.transfer_not_requested') }}</p>
        @endif
    @endif

    @if (!isset($disable_submit_transport_form))
        <form action="{{ route('front.event.transport.update.divine.step.recap', $event) }}"
              method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="d-flex justify-content-center mt-3">
                    <button
                        type="submit"
                        class="btn btn-primary next-btn mb-0">
                        {{ __('front/transport.submit_process') }}
                    </button>
                </div>
                <p class="text-danger text-center mt-2 fw-bold">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ __('front/transport.you_will_not_be_able_to_change_your_choice_later') }}
                </p>
            </div>
            @endif
        </form>
</div>
