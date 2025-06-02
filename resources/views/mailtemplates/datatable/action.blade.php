<ul class="mfw-actions">

    <li class="wg-drop">
        <button class="mfw-edit-link btn btn-sm btn-secondary dropdown-toggle me-1" type="button"
                id="dropdownMenuLink_submenu_actions_{{$data->id}}"
                data-bs-toggle="dropdown"
                aria-expanded="false"><i class="fa-solid fa-cog"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink_actions_{{$data->id}}">
            <li>
                <a target="_blank" href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#duplicate_{{ $data->id }}">
                    <i class="fa-solid fa-copy" style="color:var(--ab-green) !important;"></i>
                    Dupliquer
                </a>
            </li>
            <li>
                <a target="_blank" href="{{route('panel.mailtemplates.show', ['mailtemplate' => $data->id])}}?as=pdf" class="dropdown-item">
                    <i class="fa-solid fa-file-pdf" style="color:var(--ab-green) !important;"></i>
                    Visualiser PDF FR
                </a>
            </li>
            <li>
                <a target="_blank" href="{{route('panel.mailtemplates.show', ['mailtemplate' => $data->id])}}?as=pdf&lg=en" class="dropdown-item">
                    <i class="fa-solid fa-file-pdf" style="color:var(--ab-green) !important;"></i>
                    Visualiser PDF EN
                </a>
            </li>
            <li>
                <a target="_blank" href="{{route('panel.mailtemplates.show', ['mailtemplate' => $data->id])}}" class="dropdown-item">
                    <i class="fa-solid fa-envelope" style="color:var(--ab-green) !important;"></i>
                    Mail FR
                </a>
            </li>
            <li>
                <a target="_blank" href="{{route('panel.mailtemplates.show', ['mailtemplate' => $data->id])}}?lg=en" class="dropdown-item">
                    <i class="fa-solid fa-envelope" style="color:var(--ab-green) !important;"></i>
                    Mail EN
                </a>
            </li>
        </ul>
    </li>

    <x-mfw::edit-link :route="route('panel.mailtemplates.edit', $data)"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}" title="Archiver"/>
</ul>

<x-mfw::modal :route="route('panel.mailtemplates.destroy', $data)"
              question="Supprimer l'email <b>{{ $data->title }}</b> ?"
              reference="destroy_{{ $data->id }}"/>

<x-mfw::modal :route="route('panel.mailtemplates.duplicate', $data)"
              title="Dupliquer"
              question="Dupliquer l'email <b>{{ $data->title }}</b> ?"
              reference="duplicate_{{ $data->id }}"/>
