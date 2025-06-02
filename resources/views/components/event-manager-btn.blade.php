@props(['event'])
<a class="btn btn-sm btn-warning"
   href="{{ route('panel.manager.event.show', $event) }}"
   style="color: black">
    <i class="bi bi-bounding-box"></i>
    Gestion de l'évènement</a>
