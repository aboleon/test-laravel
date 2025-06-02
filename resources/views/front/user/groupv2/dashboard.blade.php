<x-front-logged-in-group-manager-v2-layout :event="$event">

    <div class="container">
        <div class="row g-2">
            <div class="col-12 col-lg-6">
                <div class="card border">
                    <div class="card-header">
                        <h5 class="card-title p-1">{{__('front/groups.dashboard_event_docs')}}</h5>
                        <p>
                            {{__('front/groups.dashboard_event_docs_text')}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border">
                    <div class="card-header">
                        <h5 class="card-title">
                            {{__('front/groups.dashboard_my_group')}}
                            <span class="smaller">"{{$eventGroup->group->name}}"</span>
                        </h5>
                        <span>
                            {{$eventGroup->eventGroupContacts->count()}} membres
                        </span>
                    </div>
                    <div class="card-body">
                        <a href="{{route('front.event.group.members', $event->id)}}"
                           class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i>
                            {{__('front/groups.dashboard_group_members')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 col-lg-6">
                <div class="card border">
                    <div class="card-header">
                        <h5 class="card-title">Mes commandes / factures</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{route('front.event.group.orders', $event->id)}}"
                           class="btn btn-sm btn-primary"><i class="bi bi-eye"></i>
                            Voir
                        </a>
                    </div>
                </div>
            </div>
            @if ($hasAttributions)
                <div class="col-12 col-lg-6">
                    <div class="card border">
                        <div class="card-header pb-0">
                            <h5 class="card-title">Attributions</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p>{{ __('front/groups.has_attributions_to_manage') }}</p>
                            <a href="{{ route('front.event.group.attributions.index', $event->id) }}"
                               class="btn btn-sm btn-primary"><i class="bi bi-eye"></i>
                                {{ __('ui.manage') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (session('return_code'))
        @php
            $returnCode = session('return_code');
            $returnType = session('return_type');
            $modalToShowId = ('success' === $returnType && '00000' === $returnCode) ? 'checkoutSuccessModal' : 'checkoutErrorModal';
        @endphp

        @include('front.user.group.modal.checkout-success-modal')
        @include('front.user.group.modal.checkout-error-modal')
        @push('js')
            <script>
                $(document).ready(function () {
                    $('#{{$modalToShowId}}').modal('show');
                });
            </script>
        @endpush
    @endif


</x-front-logged-in-group-manager-v2-layout>
