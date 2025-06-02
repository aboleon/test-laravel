<?php
return [
    'modules' => [
        'text' => [
            'label' => 'Texte',
            'type' => [
                'input' => 'Champ texte',
                'textarea' => 'Textarea'
            ]
        ],
        'selection' => [
            'label' => 'Sélection',
            'type' => [
                'select' => 'Liste de sélection :select',
                'checkbox' => 'Choix multiples :checkbox',
                'radio' => 'Choix uniques :radio'
            ]
        ]
    ],
    'routes' => [
        \App\Models\Account::class => 'panel.accounts.edit'
    ]
];
