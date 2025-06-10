@php
    $account = $user->account;


@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs/>
    <div>
        <livewire:front.user.identity-card-section :account="$account"/>
    </div>
    <div>
        <livewire:front.user.loyalty-card-section :account="$account"/>
    </div>
    <div>
        <div class="card border mt-4">
            <div class="card-header border-bottom text-uppercase d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-light-emphasis">{!! __('front/account.documents') !!}</h5>
            </div>
            <div class="card-body">

                {!! csscrush_inline(public_path('css/mediaclass_docs.css')) !!}
                <x-mediaclass::uploadable :model="$account"
                                          group="transport_user_docs"
                                          size="small"
                                          icon="bi bi-file-earmark-arrow-up-fill"
                                          :description="false"
                                          :nomedia="__('mediaclass.no_documents')"
                                          :label="__('front/ui.media.add_travel_documents')"/>

            </div>
        </div>
    </div>


</x-front-logged-in-layout>
