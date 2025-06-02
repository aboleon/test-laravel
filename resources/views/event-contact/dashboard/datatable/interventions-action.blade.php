<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.program.intervention.edit', [
        'event' => $event->id,
        'intervention' => $intervention_id
    ])" />
</ul>