@if($moderatorOratorItems->isNotEmpty())
    <div class="card border">
        <div class="card-header">
            <h5>{{__('front/transport.my_interventions')}}</h5>
        </div>
        <div class="card-body">
            <div class="row gx-5">
                <x-front.dashboard-card
                        :showPicto="false"
                        title="{{__('front/transport.my_interventions')}}"
                        pictoUrl="{{asset('front/images/pictograms/speaker.png')}}"
                        notBusyText="{!! __('front/dashboard.you_dont_have_interventions_yet') !!}"
                        readMoreUrl="{{route('front.event.intervention.edit', $event)}}"
                        :items="$moderatorOratorItems"
                        :show-see-action="false"
                        :use-card="false"
                        :show-title="false"
                />
            </div>
        </div>
    </div>
@endif
