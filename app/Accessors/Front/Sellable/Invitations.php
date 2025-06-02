<?php

namespace App\Accessors\Front\Sellable;

use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;
use App\Enum\ApprovalResponseStatus;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Sellable\Choosable;

class Invitations
{


    public static function getItems(Event $event, EventContact $eventContact)
    {
        $contactChoosables = EventContactSellableServiceChoosables::getEventContactChoosables($event, $eventContact);

        return $contactChoosables->map(function (Choosable $contactChoosable) use ($eventContact) {

            $status = ApprovalResponseStatus::PENDING->value;

            if ($eventContact->id === $contactChoosable->event_contact_id) {
                $status = $contactChoosable->status;
            }
            $badge = self::getStatusBadge($status);


            $texts = [];

            if ($contactChoosable->service_date) {
                $texts[] = "Date: " . $contactChoosable->service_date;
            }
            if ($contactChoosable->service_starts) {
                $texts[] = "Heure: " . $contactChoosable->service_starts->format('H\hi');
            }

            $location = null;
            if ($contactChoosable->place) {
                $location = "Lieu: " . $contactChoosable->place->name;
                if ($contactChoosable->room) {
                    $location .= " - " . $contactChoosable->room->name;
                }
            }

            if ($location) {
                $texts[] = $location;
            }


            return array_merge([
                'title' => $contactChoosable->title,
                'text' => implode('<br>', $texts),
                'actions' => [
                    [
                        'type' => 'link',
                        'class' => 'btn-primary',
                        'title' => 'Voir',
                        'url' => route('front.event.invitation.edit', $contactChoosable->event_id),
                    ],
                ],
            ], $badge);
        });
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getStatusBadge(?string $status = null)
    {
        return match ($status) {
            ApprovalResponseStatus::VALIDATED->value => self::getBadge('bg-success', 'Accepté'),
            ApprovalResponseStatus::DENIED->value => self::getBadge('bg-danger', 'Refusé'),
            ApprovalResponseStatus::PENDING->value => self::getBadge('bg-info', 'En attente'),
            default => self::getBadge('bg-secondary', 'Non renseigné'),
        };
    }

    private static function getBadge($bgClass, $text)
    {
        return [
            'badge' => [
                'class' => $bgClass . ' rounded-pill',
                'text' => $text,
            ],
        ];
    }
}