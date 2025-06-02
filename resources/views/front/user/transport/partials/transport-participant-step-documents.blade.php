<div>
    {!! csscrush_inline(public_path('css/mediaclass_docs.css')) !!}
    @if (!isset($disable_title))
        <h5 class="mt-3">{{ __('front/transport.stages.3.title') }}</h5>
    @endif
    <form action="{{route('front.event.transport.update.participant.step.documents', $event)}}"
          method="POST"
          class="mt-3"
          enctype="multipart/form-data">
        @csrf
        <div class="row border pt-3">
            <div class="col-12 mb-3">
                <label class="form-label">Documents</label>
                <x-mediaclass::uploadable :model="$account"
                                          group="transport_user_docs"
                                          size="small"
                                          icon="bi bi-file-earmark-arrow-up-fill"
                                          :description="false"
                                          :nomedia="__('mediaclass.no_documents')"
                                          :label="__('front/ui.media.add_traveil_documents')"/>

            </div>
            @if (!isset($final_submit) or isset($standalone_submit))
                <div class="col-12-6 mb-3">
                    <x-mfw::number :label="__('front/transport.labels.tickets_price')"
                                   name="ticket_price"
                                   :value="old('ticket_price', $transport?->ticket_price)"/>
                </div>
            @endif


            @if (isset($standalone_submit))
                <div class="col-12-6 mb-3">
                <button
                    type="submit"
                    class="btn btn-primary btn-sm mt-0">
                    {{ __('ui.save') }}
                </button>
                </div>
            @endif
        </div>
        @if (!isset($final_submit))
            <div class="d-flex justify-content-center mt-3 gap-2">

                <button @click="step=2"
                        type="button"
                        class="btn btn-primary next-btn mb-0">
                    {{ __('pagination.previous') }}
                </button>
                <button
                    type="submit"
                    class="btn btn-primary next-btn mb-0">
                    {{ __('pagination.next') }}
                </button>
            </div>
        @endif
    </form>

    @if (!isset($final_submit))
        <p class="mt-4 fw-bold text-danger">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <i>{{ __('front/transport.stages.3.warning') }}</i>
        </p>
    @endif
</div>
