<nav class="d-flex justify-content-between mb-3">
    <div class="nav nav-tabs" id="event_group-nav-tab" role="tablist">
        <x-mfw::tab tag="dashboard-tabpane" label="Dashboard" :active="true"/>
        <x-mfw::tab tag="general-tabpane" label="Général"/>
        <x-mfw::tab tag="the-group-tabpane" label="Groupe"/>
        <x-mfw::tab tag="the-event_group-tabpane" label="Participants"/>
        <x-mfw::tab tag="rooms-tabpane" label="Chambres bloquées"/>
        <x-mfw::tab tag="history-tabpane" label="Historique"/>
    </div>
</nav>