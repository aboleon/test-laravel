<?php
return [
    [
        'name' => 'contact',
        'confirmation' => "Votre message a bien été envoyé. Nous prendrons rapidement contact avec vous.",
        'fields' => [
            [
                'type' => 'text',
                'name' => 'last_name',
                'grid' => 'col-lg-6'
            ],
            [
                'type' => 'text',
                'name' => 'first_name',
                'grid' => 'col-lg-6'
            ],
            [
                'type' => 'text',
                'name' => 'company',
                'grid' => 'col-lg-6'
            ],
            [
                'label' => 'Activité',
                'type' => 'select',
                'name' => 'activity',
                'grid' => 'col-lg-6',
                'values' => [
                    'Promotion immobilière',
                    'Financière',
                    'Syndics',
                    'Fabricants',
                    'Intégrateurs / Installateurs',
                    'Particulier'
                ]
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'grid' => 'col-lg-6'
            ],
            [
                'type' => 'text',
                'name' => 'phone',
                'grid' => 'col-lg-6'
            ],
            [
                'type' => 'textarea',
                'name' => 'message',
                'grid' => 'col-lg-12'
            ],
        ],
    ]
];
