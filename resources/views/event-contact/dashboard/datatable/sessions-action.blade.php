<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.program.session.edit', [
        'event' => $event->id,
        'session' => $session_id
    ])" />
</ul>