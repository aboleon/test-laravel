@php
    use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;
    use App\Enum\ApprovalResponseStatus;

    $inscriptions = EventContactSellableServiceChoosables::getInvitations($sellable);
    $quantityEnabled = $sellable->invitation_quantity_enabled;
@endphp
<div>
    <table class="table inscriptions-table">
        <thead>
        <tr>
            <th>
                <input type="checkbox" x-model="allChecked" @click="toggleAll">
            </th>
            <th>Participant</th>
            <th>Quantité</th>
            <th>Statut</th>
            <th>Annulation Commande</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($inscriptions as $inscription)
            <tr>
                <td>
                    <input type="checkbox"
                           value="{{ $inscription->id }}"
                           data-id="{{ $inscription->id }}"
                           x-model="selected"
                           @change="checkIfAllSelected">
                </td>
                <td>
                    <a href="{{route("panel.manager.event.event_contact.edit", [$event, $inscription->eventContact])}}">{{$inscription->eventContact->user->names()}}</a>
                </td>
                <td>
                    @php

                        $s = '';
                        $quantityAccepted = $inscription->invitation_quantity_accepted;
                        $accepted = $inscription->status === ApprovalResponseStatus::VALIDATED->value;
                        if(!$quantityEnabled){
                            $s = $accepted ? "1" : "0";
                        }
                        else{
                            $s = "0";
                            if($accepted){
                                $s = "1";
                                if ($quantityAccepted){
                                    $s = "2";
                                }
                            }
                        }
                    @endphp
                    {{$s}}
                </td>
                <td>{{ApprovalResponseStatus::translated($inscription->status)}}</td>
                <td>
                    @if($inscription->eventContact?->order_cancellation)
                        <x-back.order-cancellation-pill />
                    @endif
                </td>
                <td>
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#delete-inscription-modal"
                            data-id="{{ $inscription->id }}"
                            wire:click="inscriptionId={{ $inscription->id }}"
                            data-name="{{ $inscription->eventContact->user->names() }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="modal fade"
         id="delete-inscription-modal"
         tabindex="-1"
         aria-labelledby="delete-inscription-modalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="delete-inscription-modalLabel">Supprimer
                        l'inscription</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette inscription ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="button"
                            class="btn btn-danger"
                            @click="$wire.deleteInscription(); $('#delete-inscription-modal').modal('hide')">
                        Supprimer
                        <x-front.livewire-ajax-spinner target="deleteInscription" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
