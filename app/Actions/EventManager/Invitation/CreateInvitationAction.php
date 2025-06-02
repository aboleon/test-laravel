<?php

namespace App\Actions\EventManager\Invitation;

use App\Enum\ApprovalResponseStatus;
use App\Models\EventManager\Sellable;
use App\Models\EventManager\Sellable\EventContactSellableServiceChoosable;
use MetaFramework\Traits\Responses;
use Throwable;
use App\Accessors\EventContactAccessor;

class CreateInvitationAction
{
    use Responses;

    public function __construct()
    {
        $this->enableAjaxMode();
    }

    public function createInvitation(): array
    {
        $invitationId = request('invitation_id');
        $participantIds = request('participant_id', []);
        $response = request('response');
        $quantity = request('quantity');

        if (empty($participantIds)) {
            $this->responseError("Veuillez sélectionner au moins un participant.");
            goto end;
        }

        if (null === $invitationId) {
            $this->responseError("Invitation non renseignée.");
            goto end;
        }

        $sellable = Sellable::find($invitationId);
        if (!$sellable) {
            $this->responseError("Invitation #$invitationId non trouvée.");
            goto end;
        }

        if (!$sellable->is_invitation) {
            $this->responseError("Les inscriptions de ce type ne sont disponibles que pour les prestations choix.");
            goto end;
        }

        if (!$sellable->stock_unlimited) {
            if (!$sellable->stock) {
                $this->responseError("Il faut configurer un stock initial ou indiquer si le stock est illimité.");
                goto end;
            }
        }

        $invitationQuantityEnabled = $sellable->invitation_quantity_enabled;

        if (null === $response) {
            $this->responseError("Veuillez renseigner une réponse.");
            goto end;
        }

        if ("1" === $response && $invitationQuantityEnabled && null === $quantity) {
            $this->responseError("Veuillez renseigner une quantité.");
            goto end;
        }

        try {
            $status = $response === "1" ? ApprovalResponseStatus::VALIDATED->value : ApprovalResponseStatus::DENIED->value;
            $createdCount = 0;
            $errors = [];

            foreach ($participantIds as $participantId) {
                // Vérifier si l'invitation existe déjà pour ce participant
                $existingInvitation = EventContactSellableServiceChoosable::where('event_contact_id', $participantId)
                    ->where('choosable_id', $invitationId)
                    ->first();

                if ($existingInvitation) {
                    $participants = EventContactAccessor::selectableByEvent($sellable->event);
                    $participantName = $participants[$participantId] ?? "Participant #$participantId";
                    $errors[] = "L'invitation pour le participant $participantName existe déjà.";
                    $notCreated[] = $participantName;
                    continue;
                }

                // Créer la nouvelle invitation
                $ecc = new EventContactSellableServiceChoosable();
                $ecc->event_contact_id = $participantId;
                $ecc->choosable_id = $invitationId;
                $ecc->status = $status;
                $ecc->invitation_quantity_accepted = ("2" === $quantity) ? 1 : null;
                $ecc->save();

                $createdCount++;
            }

            if ($createdCount > 0) {
                $message = $createdCount === 1
                    ? "L'invitation a été créée."
                    : "$createdCount invitations ont été créées.";

                if (!empty($errors)) {
                    $message .= " Cependant, " . count($errors) . " invitation(s) n'ont pas pu être créées. L'invitation existe déjà pour ces participants : <br>" . implode(" / ", $notCreated);
                }

                $this->responseSuccess($message);
            } else {
                if (!empty($notCreated)) {
                    $this->responseError("Aucune invitation n'a pu être créée. L'invitation existe déjà pour ces participants : <br>" . implode(" / ", $notCreated));
                } else {
                    $this->responseError("Aucune invitation n'a pu être créée.");
                }
            }

        } catch (Throwable $e) {
            $this->responseException($e);
        }

        end:
        return $this->fetchResponse();
    }
}
