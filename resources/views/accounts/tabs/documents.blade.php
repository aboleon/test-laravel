<div class="tab-pane fade" id="documents-tabpane" role="tabpanel" aria-labelledby="documents-tabpane-tab">

    <div class="mt-4">

        <div class="row m-0">
            <div class="col-md-12 mb-4 ps-0">
                {!! csscrush_inline(public_path('css/mediaclass_docs.css')) !!}
                <x-mediaclass::uploadable :model="$account"
                                          group="transport_user_docs"
                                          size="small"
                                          icon="bi bi-file-earmark-arrow-up-fill"
                                          :description="false"
                                          :nomedia="__('mediaclass.no_documents')"
                                          :label="__('front/ui.media.add_traveil_documents')"/>
            </div>
        </div>
    </div>

</div>
