<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Prestations</span> &raquo;
            <span>Configuration</span>
        </h2>
        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>
            <a class="btn btn-sm btn-secondary mx-2"
               href="{{ route('panel.manager.event.deposit.index', $event) }}">
                <i class="fa-solid fa-euro"></i>
                Cautions
            </a>

            <x-back.topbar.separator />

            <x-back.topbar.edit-combo
                    :wrap="false"
                    :event="$event"
                    :use-create-route="false"
                    :show-index-route="false"
            />

        </div>


    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner />
        <x-mfw::response-messages />

        <form method="post" action="{{ $route }}" id="wagaia-form">
            @csrf
            @if ($data->id)
                @method('PUT')
            @endif

            <h4>Saisie d'une caution GRANT</h4>

            <div class="row pt-3">
                <div class="col-md-6 pe-sm-5">
                    <div class="row mb-4 mfw-line-separator pb-4">

                        <div class="col-md-4">
                            <x-mfw::number min="1"
                                           name="amount"
                                           label="Montant"
                                           :value="$error ? old('amount') : ($data->amount ?: 1)" />
                        </div>
                    </div>
                    @include('events.manager.grant.deposit.grant-binded-location')
                </div>
                <div class="col-md-6 ps-sm-5">
                    <div class="grant_deposit">
                        <h4>Types de participations</h4>
                        <div id="participation_types_grant">
                            <x-participation-types :filter="true"
                                                   :subset="$event->participations->pluck('id')->toArray()"
                                                   name="grant_participation_types"
                                                   :affected="$error ? old('grant_participation_types') : $data->participations->pluck('id')" />
                        </div>
                    </div>
                </div>
            </div>

            @push('js')
                <script>

                  activateEventManagerLeftMenuItem('grant-deposits');

                  $('#sellable_has_deposit').click(function() {
                    $('.sellable_deposit_is_grant').toggleClass('d-none');
                  });
                  $('#sellable_deposit_is_grant').click(function() {
                    $('.grant_deposit').toggleClass('d-none');
                  });
                </script>
            @endpush


        </form>
    </div>

</x-event-manager-layout>
