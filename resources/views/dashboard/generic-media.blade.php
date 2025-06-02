<x-backend-layout>

    <div>

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Images génériques
            </h2>
        </x-slot>

    </div>
    <div class="shadow p-3 mb-5 bg-body-tertiary rounded">
        <div class="row">

            @foreach($model->mediaclassSettings() as $mediaKey => $mediaSetting)
                <div class="col-sm-4">
                    <x-mediaclass::uploadable :model="$model"
                                              :ghost="true"
                                              :description="false"
                                              :group="$mediaKey"/>
                </div>
            @endforeach

        </div>
        {{--
             <x-mediaclass::uploadable :model="$model"
                                       :ghost="true"
                                       group="banner_medium"/>--}}
    </div>
</x-backend-layout>
