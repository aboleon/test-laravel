@if($transport)

    <div class="card mb-3">
        <div class="card-body">
            <h4>Documents</h4>
            <div class="tr-participant">
                <x-mediaclass::uploadable :model="$account"
                                          group="transport_user_docs"
                                          size="small"
                                          icon="bi bi-file-earmark-arrow-up-fill"
                                          :description="false"
                                          :nomedia="__('mediaclass.no_documents')"
                                          label="Documents fournis par le participant"/>
            </div>
            <div class="tr-divine">
                <x-mediaclass::uploadable :model="$account"
                                          group="transport_docs"
                                          size="small"
                                          icon="bi bi-file-earmark-arrow-up-fill"
                                          :description="false"
                                          :nomedia="__('mediaclass.no_documents')"
                                          label="Ajouter des documents pour le participant"/>
            </div>
        </div>
    </div>
@endif
