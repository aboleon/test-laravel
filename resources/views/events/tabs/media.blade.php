<div class="tab-pane fade" id="media-tabpane" role="tabpanel" aria-labelledby="media-tabpane-tab">

    @foreach($data->mediaclassSettings() as $mediaKey => $mediaSetting)
        <x-mediaclass::uploadable :model="$data"
                                  :description="false"
                                  :group="$mediaKey"/>
    @endforeach

</div>
