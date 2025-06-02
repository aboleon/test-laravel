<?php

return [
    'paybox' => [
        'site' => env('PAYBOX_SITE'),
        'rang' => env('PAYBOX_RANG'),
        'id' => env('PAYBOX_IDENTIFIANT'),
        'hmac' => env('PAYBOX_HMAC_KEY'),
        'preprod' => env('PAYBOX_USE_PREPROD'),
        'cle' => env('PAYBOX_CLE'),
    ]
];
