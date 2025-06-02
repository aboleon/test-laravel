<ul class="mfw-actions">
    <li>
        <a href="#" data-model-id="{{ $data->pec_distribution_id }}"
           data-identifier="pec-{{ $data->pec_distribution_id }}"
           class="btn btn-info btn-sm pec-reassigner"
           data-bs-toggle="modal"
           data-bs-target="#mfw-simple-modal"
           data-modal-id="modal-pec-reassign" data-title="Réallocation de la PEC"
           data-btn-confirm="Assigner"
           data-btn-confirm-class="btn-danger"
           data-btn-cancel="Fermer">
            <i class="fas fa-refresh" style="font-size: smaller"></i> </a>
    </li>

</ul>
