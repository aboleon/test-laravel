<?php

return [
    'amount_before_tax' => 'Montant HT',
    'booking_finalized_for_this_step' => 'Réservation finalisée pour cette étape',
    'comment_not_visible_by_pax' => 'Commentaire (non visible par pax)',
    'departure_transport' => "Transport aller",
    'desired_transport_management' => 'Gestion du transport souhaitée',
    'documents' => "Documents",
    'maximum_reimbursement' => 'Remboursement max',
    'participant_departure_comment' => 'Commentaire aller du participant',
    'participant_return_comment' => 'Commentaire retour du participant',
    'participant_ticket_submission_notification' => "A l'enregistrement, cela va envoyer un mail au participant pour l'informer du dépôt de ses billets / documents",
    'reference_info_participant' => 'Référence (information pour le participant)',
    'reimbursement' => 'Remboursement',
    'financing' => 'Financement',
    'return_transport' => "Transport retour",
    'shuttle_time' => 'Heure navette',
    'ticket_total' => 'Total des billets',
    'total_amount_incl_tax' => 'Montant TTC',
    'transfer_info' => 'Infos transfert',
    'transfer_requested' => 'Transfert demandé',
    'transport' => 'Transport',
    'travel_preferences' => 'Préférences pour le voyage',
    'undesired_transport_management' => 'Gestion du transport non souhaitée (Participant ou Non nécessaire)',
    'visible_by_admin_only' => 'Visible par admin seulement',


    //--------------------------------------------
    // form labels
    //--------------------------------------------
    'labels' => [
        'departure_end_time' => 'Heure arrivée',
        'departure_start_date' => 'Date départ',
        'departure_start_time' => 'Heure départ',
        'desired_transport_management' => 'Gestion',
        'return_end_time' => 'Heure arrivée',
        'return_start_date' => 'Date départ',
        'return_start_time' => 'Heure départ',
    ],

    //--------------------------------------------
    // errors
    //--------------------------------------------
    'errors' => [
        'participant_not_found' => 'Participant non trouvé',
        'start_date_over_departure_date' => "La date de départ ne doit pas être après la date de retour",
        'departure_date_over_start_date' => "La date de retour ne doit pas être avant la date de départ",
    ],

    //--------------------------------------------
    // messages
    //--------------------------------------------
    'messages' => [
        'deleted' => 'Le transport est supprimé.',
    ],


    //--------------------------------------------
    // column names
    //--------------------------------------------
    'name' => 'Nom',
    'pec' => 'PEC',
    'participation_type' => 'Type de participation',
    'departure_online' => 'Aller en ligne',
    'departure_step' => 'Etape',
    'departure_transport_type' => 'Type',
    'departure_start_date' => 'Date',
    'departure_start_time' => 'Heure',
    'departure_start_location' => 'Départ',
    'departure_end_time' => 'Heure',
    'departure_end_location' => 'Arrivée',
    'return_online' => 'Retour en ligne',
    'return_step' => 'Etape',
    'return_transport_type' => 'Type',
    'return_start_date' => 'Date',
    'return_start_time' => 'Heure',
    'return_start_location' => 'Départ',
//    'return_end_time' => 'Time',
    'return_end_location' => 'Arrivée',
    'transfer' => 'Transfert',
    'price_before_tax' => 'Montant HT',
    'price_after_tax' => 'Montant TTC',
    'max_reimbursement_amount' => 'Rb max',
    'has_documents' => 'Docs',
    'actions' => 'Actions',
];
