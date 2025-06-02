@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade"
     id="the-group-tabpane"
     role="tabpanel"
     aria-labelledby="the-group-tabpane-tab">
    <div class="d-flex justify-content-end">
        <x-save-btns />
    </div>

    @include('groups.partials.edit_body')
</div>
