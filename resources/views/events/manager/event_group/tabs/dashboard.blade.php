@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade show active"
     id="dashboard-tabpane"
     role="tabpanel"
     aria-labelledby="dashboard-tabpane-tab">
    <div class="d-flex gap-2 mb-3 mt-4">
        <a class="btn btn-sm btn-green" href="{{ route('panel.manager.event.orders.create', ['event' => $event, 'group' => $eventGroup->group_id]) }}">
            <i class="fa-solid fa-plus"></i>
            Nouvelle commande
        </a>
        <a class="btn btn-sm btn-warning"
           href="{{route('pdf-printer', ['type' => 'eventGroupConfirmation', 'identifier' => encrypt($eventGroup->id)])}}"
           target="_blank">
            <i class="fa-solid fa-file-pdf"></i>
            Confirmation groupe PDF
        </a>
        <a class="btn btn-sm btn-blue-gray" href="#"
           data-bs-toggle="modal"
           data-bs-target="#send_event_group_confirmation">
            <i class="fa-solid fa-envelope"></i>
            Confirmation groupe Mail
        </a>
        <a class="btn btn-sm btn-red" href="#"
           data-bs-toggle="modal"
           data-bs-target="#generate_event_group_for_contact">
            <i class="fa-solid fa-file-pdf"></i>
            Confirmation participants PDF
        </a>

        @include('events.manager.event_group.modal.select-group-contact')

        <a class="btn btn-sm btn-violet" href="#"
           data-bs-toggle="modal"
           data-bs-target="#mfwDynamicModal"
           data-modal-content-url="{{ route('panel.modal', ['requested' => 'sendEventContactConfirmationByGroup', 'group_id' => $eventGroup->id]) }}"
           data-modal-on-success="uncheckContact">
            <i class="fa-solid fa-envelope-open-text"></i>
            Confirmation participants Mail
        </a>

        <x-mfw::modal :route="route('panel.mailer', ['type' => 'sendEventGroupConfirmation', 'identifier' => encrypt($eventGroup->id)])"
                      title="Envoyer la confirmation par email ?"
                      :params="['uuid' => encrypt($eventGroup->id)]"
                      class="send_event_group_confirmation"
                      question="Envoyer le PDF de confirmation à l'adresse e-mail du contact principal."
                      reference="send_event_group_confirmation"/>



        @push('js')
            <script src="{{ asset('js/eventmanager/event_group_confirmation.js') }}"></script>
        @endpush
        @pushonce('modals')
            @include('mfw-modals.launcher')
        @endpushonce
    </div>
    <div class="row pt-3">
        @include('events.manager.event_group.tabs.dashboard.orders')
    </div>

</div>
