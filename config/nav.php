<?php
return [
    'selectables' => [
        'page' => 'Pages',
        'formation_category'=> 'Categories de formations',
        'listable'=> 'Pages listes',
    ],
    'custom_selectables' => [
        [
            'type' => 'home',
            'title' => "Page d'accueil",
            'url' => '/'
        ],
    ],
    'urls' => [
        'formation_category'=> 'formations',
        'news' => 'actualites'
    ],
    'defaults' => [
        'formation_category' => [
            'label' => 'Non catégorisées',
            'url' => 'non-categorisees'
        ]
    ]
];
