@props([
    'title' => "Visible / accessible uniquement quand l'app est en mode debug",
])
@if(config('app.debug'))
    <span class="bg-dark-subtle devcode"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          title="{{$title}}"
          data-bs-title="{{$title}}"><i class="bi bi-braces"></i></span>
@endif
