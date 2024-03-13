<?php

return [
    'navigation' => [
        'token' => [
            'group' => 'Settings',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        'panel_prefix' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
];
