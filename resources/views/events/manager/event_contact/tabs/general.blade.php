@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade pt-4"
     id="general-tabpane"
     role="tabpanel"
     aria-labelledby="general-tabpane-tab">

        <form id="form_event_contact_general" method="post" action="{{route('panel.manager.event.event_contact.update', [
        'event' => $event,
        'event_contact' => $eventContact,
        ])}}">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="general">
            <x-participation-type-select name="participation_type_id"
                                         :group="true"
                                         :affected="$error ? old('participation_type_id') : $eventContact->participation_type_id"/>

            <x-mfw::radio
                name="is_attending"
                :values="[1 => 'Oui', 0 => 'Non']"
                label="Présence congrès"
                :default="(int)$eventContact->is_attending"
                :affected="$error ? old('is_attending') : ($eventContact->id ? (int)$eventContact->is_attending : 0)"/>


            <x-mfw::textarea name="comment"
                             label="Commentaires"
                             :value="$error ? old('comment') : $eventContact->comment"/>

        </form>
</div>
