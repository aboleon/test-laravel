<?php

/**
 * User roles with their properties.
 *
 * @return array
 */

return [
    'dev' => [
        'id' => 1,
        'label' => 'dev',
        'profile' => 'dev',
        'subgroup' => 'admin',
    ],
    'super-admin' => [
        'id' => 2,
        'label' => 'Administrateur',
        'profile' => 'admin',
        'subgroup' => 'admin',
    ],
];
