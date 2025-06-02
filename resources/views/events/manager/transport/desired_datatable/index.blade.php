<div class="col-12">
    <div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
        <header class="mfw-line-separator mb-3">
            <h4>Gestion du transport souhaitée</h4>
        </header>

            <x-datatables-mass-delete model="EventManager\Transport\ParticipantTransport" name="title" question="<strong>Est-ce que vous confirmez la suppression des entrées sélectionnées?</strong>"/>
            {!! $desired_dataTable->table()  !!}

        @push('js')
            {{ $desired_dataTable->scripts() }}
        @endpush
    </div>
</div>
