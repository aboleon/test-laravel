@php
    $locale = app()->getLocale();
    $attribution_messages = [
        'fr' => [
            'at_least_one_member' => 'Vous devez sélectionner au moins un membre.',
            'at_least_one_atttribution' => 'Vous devez sélectionner au moins une attribution.',
            'groupmax' => "Limite d'affectation atteinte.La quantité maximale d'attribution est de ",
            'minimal' => "La quantité minimale d'attribution est de 1.",
            'overflow' => "Vous ne pouvez pas attribuer plus de quantité de ce qu'il en reste.",
            'unsufficient' => "Vous ne pouvez pas attribuer cette quantité à autant de membres",
            'already_assigned' => "Déjà attribué",
            'already_assigned_elsewhere' => "Déjà attribué sur d'autres commandes",
            'already_booked_elsewhere' => "Déjà réservé sur d'autres commandes",
        ],
        'en' => [
            'at_least_one_member' => 'You have to pick at least one member.',
            'at_least_one_atttribution' => 'You have to pick at least one attribution.',
            'groupmax' => "Assignment limit reached. The maximum assignment quantity is ",
            'minimal' => "The minimum assignment quantity is 1.",
            'overflow' => "You cannot assign more than the remaining quantity.",
            'unsufficient' => "You cannot assign this quantity to so many members.",
            'already_assigned' => "Already assigned",
            'already_assigned_elsewhere' => "Already assigned on other orders",
            'already_booked_elsewhere' => "Already réservé on other orders",
        ],
    ];
@endphp
<div id="attribution-messages" class="d-none">
    @foreach($attribution_messages[$locale] as $key => $message)
        <span class="{{ $key }}">{{ $attribution_messages[$locale][$key] }}</span>
    @endforeach
</div>
