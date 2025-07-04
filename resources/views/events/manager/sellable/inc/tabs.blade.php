<nav class="d-flex justify-content-between mb-3">
    <div class="nav nav-tabs" id="event-sellable-nav-tab" role="tablist">
        <x-mfw::tab tag="config-tabpane" label="Configuration & Prix" :active="true"/>
        <x-mfw::tab tag="texts-tabpane" label="Descriptifs"/>
        <x-mfw::tab tag="ptypes-tabpane" label="Participations & Professions"/>
        <x-mfw::tab tag="inscriptions-tabpane" label="Inscriptions"/>
        <x-mfw::tab tag="sage-tabpane" label="SAGE"/>
    </div>
</nav>
@push('js')
    <script>
        $(function() {
            $('ul.side-menu li.prestations').addClass('current-page');
        });
    </script>
@endpush
