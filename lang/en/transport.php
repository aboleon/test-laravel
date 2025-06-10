<?php

return [
    'amount_before_tax'                          => 'Amount Before Tax',
    'booking_finalized_for_this_step'            => 'Booking Finalized for this Step',
    'comment_not_visible_by_pax'                 => 'Comment (not visible by pax)',
    'departure_transport'                        => "Departure Transport",
    'desired_transport_management'               => 'Desired Transport Management',
    'documents'                                  => "Documents",
    'maximum_reimbursement'                      => 'Maximum Reimbursement',
    'participant_departure_comment'              => 'Participant\'s Departure Comment',
    'participant_return_comment'                 => 'Participant\'s Return Comment',
    'participant_ticket_submission_notification' => "At registration, this will send an email to the participant to inform them of the submission of their tickets/documents",
    'reference_info_participant'                 => 'Reference (Information for the Participant)',
    'reimbursement'                              => 'Reimbursement',
    'financing'                                  => 'Financement',
    'return_transport'                           => "Return Transport",
    'shuttle_time'                               => 'Shuttle Time',
    'ticket_total'                               => 'Ticket Total',
    'total_amount_incl_tax'                      => 'Total Amount (incl. tax)',
    'transfer_info'                              => 'Transfer Information',
    'transfer_requested'                         => 'Transfer Requested',
    'transport'                                  => 'Transport',
    'travel_preferences'                         => 'Travel Preferences',
    'undesired_transport_management'             => 'Undesired Transport Management (Participant or Not Necessary)',
    'visible_by_admin_only'                      => 'Visible by Admin Only',

    //--------------------------------------------
    // form labels
    //--------------------------------------------
    'labels'                                     => [
        'departure_end_time'           => 'Arrival Time',
        'departure_start_date'         => 'Departure Date',
        'departure_start_time'         => 'Departure Time',
        'desired_transport_management' => 'Management',
        'return_end_time'              => 'Arrival Time',
        'return_start_date'            => 'Departure Date',
        'return_start_time'            => 'Departure Time',
        'ticket_price'                 => "Ticket price",
        'tickets_price'                => "Tickets price",
    ],

    //--------------------------------------------
    // errors
    //--------------------------------------------
    'errors'                                     => [
        'participant_not_found' => 'Participant not found',
        'start_date_over_departure_date' => "Departure date cannot be after return date",
        'departure_date_over_start_date' => "Return date cannot be before start date",
    ],

    //--------------------------------------------
    // messages
    //--------------------------------------------
    'messages'                                   => [
        'deleted' => 'The transport was deleted.',
    ],

    //--------------------------------------------
    // column names
    //--------------------------------------------
    'name'                                       => 'Name',
    'pec'                                        => 'PEC',
    'participation_type'                         => 'Participation Type',
    'departure_online'                           => 'Departure Online',
    'departure_step'                             => 'Step',
    'departure_transport_type'                   => 'Type',
    'departure_start_date'                       => 'Date',
    'departure_start_time'                       => 'Time',
    'departure_start_location'                   => 'Departure',
//    'departure_end_time' => 'Time',
    'departure_end_location'                     => 'Arrival',
    'return_online'                              => 'Return Online',
    'return_step'                                => 'Step',
    'return_transport_type'                      => 'Type',
    'return_start_date'                          => 'Date',
    'return_start_time'                          => 'Time',
    'return_start_location'                      => 'Departure',
//    'return_end_time' => 'Time',
    'return_end_location'                        => 'Arrival',
    'transfer'                                   => 'Transfer',
    'price_before_tax'                           => 'Price before tax',
    'price_after_tax'                            => 'Price with tax',
    'max_reimbursement_amount'                   => 'Max Rimb',
    'has_documents'                              => 'Docs',
    'actions'                                    => 'Actions',


    // validation labels
];
