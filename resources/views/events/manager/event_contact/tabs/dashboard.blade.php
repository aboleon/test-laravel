@php
    $error = $errors->any();
@endphp

<div class="tab-pane fade show active"
     id="dashboard-tabpane"
     role="tabpanel"
     aria-labelledby="dashboard-tabpane-tab">
    <div class="d-flex gap-2 mb-3">
        <a class="btn btn-sm btn-green"
           href="{{ route('panel.manager.event.orders.create', ['event' => $event, 'contact' => $eventContact->user_id]) }}">
            <i class="fa-solid fa-plus"></i>
            Nouvelle commande
        </a>
        <a class="btn btn-sm btn-warning text-dark"
           href="{{ route('panel.manager.event.orders.create', ['event' => $event, 'contact' => $eventContact->user_id]) }}&as_orator">
            <i class="fa-solid fa-plus"></i>
            Nouvelle commande intervenant
        </a>

        @if($eventContactAccessor->hasOrdersOrGroupOrders())
            <a class="btn btn-sm btn-danger"
               href="{{route('pdf-printer', ['type' => 'eventConfirmation', 'identifier' => $eventContact->uuid])}}"
               target="_blank">
                <i class="fa-solid fa-file-pdf"></i>
                Confirmation PDF
            </a>
            <a class="btn btn-sm btn-secondary"
               href="#"
               data-bs-toggle="modal"
               data-bs-target="#send_event_contact_confirmation">
                <i class="fa-solid fa-envelope"></i>
                Envoyer la confirmation
            </a>

            <x-mfw::modal :route="route('panel.mailer', ['type' => 'sendEventContactConfirmation', 'identifier' => $eventContact->uuid])"
                          title="Envoyer la confirmation par email ?"
                          :params="['uuid' => $eventContact->uuid]"
                          class="send_event_contact_confirmation"
                          question="Envoyer le PDF de confirmation à l'adresse e-mail du contact."
                          reference="send_event_contact_confirmation"/>

            @push('js')
                <script src="{{ asset('js/eventmanager/send_event_confirmation.js') }}"></script>
            @endpush
        @endif

    </div>

    @include('lib.datatable')

    <form id="form_event_contact_dashboard" method="post" action="{{ route('panel.manager.event.event_contact.update', [
        'event' => $event,
        'event_contact' => $eventContact,
        ]) }}">
        @csrf
        @method('PUT')


        <input type="hidden" name="section" value="dashboard">
        <div class="row mb-4 mt-5">
            <div class="col-12">
                <x-mfw::checkbox :switch="true"
                                 name="order_cancellation"
                                 value="1"
                                 label="Annulation"
                                 :affected="old('order_cancellation', $eventContact->order_cancellation)" />
            </div>
        </div>
    </form>

    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.orders')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.attributions')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.deposits')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.transports')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.interventions')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.sessions')
    </div>
    <div class="row pt-3">
        @include('events.manager.event_contact.tabs.dashboard.choosables')
    </div>
</div>
