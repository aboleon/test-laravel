<nav class="d-flex justify-content-between mb-3">
    <div class="nav nav-tabs" id="event_contact-nav-tab" role="tablist">
        <x-mfw::tab tag="dashboard-tabpane" label="Dashboard" :active="true"  />
        <x-mfw::tab tag="general-tabpane" label="Général" />
        <x-mfw::tab tag="contact-tabpane" label="Contact" />
        <x-mfw::tab tag="pec-tabpane" label="PEC" />
    </div>
</nav>
@push('js')
    <script>
      activateEventManagerLeftMenuItem('event-contacts');
    </script>
@endpush
