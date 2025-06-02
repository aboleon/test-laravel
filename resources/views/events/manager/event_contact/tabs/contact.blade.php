@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade"
     id="contact-tabpane"
     role="tabpanel"
     aria-labelledby="contact-tabpane-tab">
    @include('accounts.partials.edit_body', ['event_contact_id' => $eventContact->id])
</div>
