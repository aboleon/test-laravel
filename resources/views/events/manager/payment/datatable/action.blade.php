<ul class="mfw-actions">
    <a href="{{ route('panel.manager.event.orders.edit', [
        'event'=> $event->id,
        'order' => $data->order_id,
    ]) }}?tab=payments-tabpane-tab" class="mfw-edit-link btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
       data-bs-title="Voir">
        <i class="fas fa-eye"></i>
    </a>
</ul>
