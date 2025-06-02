<style>
    .row-participant, .row-participant td{
        background: #bcedef !important;
    }
</style>
<div class="col-12">
    <div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
        <header class="mfw-line-separator mb-3">
            <h4>Gestion du transport non souhaitée ("Participant" ou "Non nécesssaire")</h4>
        </header>
        <p class="border border-2 bg-body-secondary p-3">Lignes avec fond bleu = gestion souhaitée Participant</p>

        <x-datatables-mass-delete model="EventManager\Transport\ParticipantTransport"
                                  name="title"
                                  question="<strong>Est-ce que vous confirmez la suppression des entrées sélectionnées?</strong>" />
        {!! $undesired_dataTable->table()  !!}

        @push('js')
            {{ $undesired_dataTable->scripts() }}
        @endpush
    </div>
</div>



