@php
    $docs = collect();
@endphp

<style>
    .table-first-col-special {
        td:first-child {
            /*font-weight: bold;*/
        }

    }
</style>
<div class="p-2 mt-3">
    <h5>{{__('front/transport.step_1_departure')}}</h5>
    <x-eventManager.transport.transport-departure-info :transport="$transport" />

    <h5>{{__('front/transport.step_2_return')}}</h5>
    <x-eventManager.transport.transport-return-info :transport="$transport" />

    <h5>{{__('front/transport.step_3_invoices')}}</h5>

    {!! csscrush_inline(public_path('css/mediaclass_docs.css')) !!}
    <x-mediaclass::uploadable :model="$account"
                              group="transport_user_docs"
                              size="small"
                              icon="bi bi-file-earmark-arrow-up-fill"
                              :description="false"
                              :nomedia="__('mediaclass.no_documents')"
                              :label="__('front/ui.media.add_traveil_documents')"/>

    <table class="table-first-col-special table table-sm table-bordered">
        <tr>
            <th class="w-200px">{{ __('front/transport.labels.tickets_price') }}</th>
            <td>
                {{ \MetaFramework\Accessors\Prices::readableFormat($transport->ticket_price) }}
            </td>
        </tr>
    </table>


    @if($isTransferEligible)
        <h5>{{__('front/transport.step_4_transfer')}}</h5>

        @if($transport->transfer_requested)
            <p class="fw-bold text-info"><i class="bi bi-check-circle"></i> {{__('front/transport.transfer_requested')}}</p>
        @else
            <p class="fw-bold text-danger"><i class="bi bi-x-circle"></i> {{__('front/transport.transfer_not_requested')}}</p>
        @endif
    @endif


    <form action="{{route('front.event.transport.update.participant.step.recap', $event)}}"
          method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="d-flex justify-content-center mt-3">
                <button
                        type="submit"
                        class="btn btn-primary next-btn mb-0">{{__('front/transport.submit_process')}}
                </button>
            </div>
            <p class="text-danger text-center mt-2 fw-bold">
                <i class="bi bi-exclamation-triangle me-1"></i> {{__('front/transport.you_will_not_be_able_to_change_your_choice_later')}}
            </p>
        </div>
    </form>
</div>
