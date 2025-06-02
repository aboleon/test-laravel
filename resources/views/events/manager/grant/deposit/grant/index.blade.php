<h4>Caution GRANT</h4>
@if ($event->grantDeposit)

    <table class="table-bordered table">
        <thead>
        <tr>
            <th>Montant</th>
            <th>Application géographique</th>
            <th>Type de participation</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $event->grantDeposit->amount }} €</td>
            <td>
                @forelse($event->grantDeposit->locations as $location)
                    {{ ltrim($location->locality.', '. \MetaFramework\Accessors\Countries::getCountryNameByCode($location->country_code),',')  }}
                    <br/>
                @empty
                    Aucune
                @endforelse
            </td>
            <td>
                @php
                    $participations = $event->grantDeposit->participations->pluck('id')->toArray();
                    $participation_types = \App\Accessors\Dictionnaries::participationTypes()->flatten()->filter(fn($item) => in_array($item->id, $participations))->groupBy('group');
                @endphp
                @forelse($participation_types as $group_key => $group)
                    <strong>{{ \App\Enum\ParticipantType::translated($group_key) }}</strong> : {{ $group->pluck('name')->join(', ') }}
                    <br>
                @empty
                    Tous
                @endforelse
            </td>
            <td>
                <ul class="mfw-actions">
                    <x-mfw::edit-link :route="route('panel.manager.event.grantdeposit.edit', ['event'=>$event, 'grantdeposit' => $event->grantDeposit->id])"/>
                    <x-mfw::delete-modal-link reference="{{ $event->grantDeposit->id }}"/>
                </ul>
                <x-mfw::modal :route="route('panel.manager.event.grantdeposit.destroy', ['event'=>$event, 'grantdeposit' => $event->grantDeposit->id])"
                              title="{{__('ui.delete')}}"
                              question="Supprimer cette caution GRANT ?"
                              reference="destroy_{{ $event->grantDeposit->id }}"/>
            </td>
        </tr>
        </tbody>
    </table>

@else

    <x-mfw::alert message="Aucune caution GRANT n'est saisie pour cet évènement"/>

    <a href="{{ route('panel.manager.event.grantdeposit.create', $event) }}" class="btn btn-sm btn-success">Ajouter</a>

@endif
