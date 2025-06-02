@php
    $error = $errors->any();
@endphp
@if($catalog->entries->isEmpty())
    <x-mfw::alert type="warning" message="Aucune catégorie de catalogue n'est saisie"/>
@else
    <table class="table">
        <thead>
        <tr>
            <th>Catégorie</th>
            <th>Articles</th>
        </tr>
        </thead>
        <tbody>
        @forelse($catalog->entries as $catalog_entry)
            @if ($sellables_grouped->has($catalog_entry->id))
                <tr>
                    <td>
                        {{ $catalog_entry->name }}
                    </td>
                    <td>
                        @foreach($sellables_grouped->get($catalog_entry->id) as $sellable)
                            <div class="d-flex justify-content-between my-1">
                                <x-mfw::checkbox :switch="true" name="event_catalog[]" :value="$sellable->id" :label="$sellable->title" :affected="collect($error ? old('event_catalog') : ($data->serialized_config['event_catalog'] ?? []))"/>

                                @if ($data->id)
                                    <a href="#" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-title="Voir la description" data-bs-target="#mfwDynamicModal" data-modal-content-url="{{ route('panel.modal', ['requested' => 'sellableByEvent', 'id'=>$sellable->id, 'event_id'=>$data->id]) }}">
                                        <i class="fa-solid fa-pen"></i></a>
                                @endif
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="2">
                    Aucun article n'est saisi.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

@endif
