<?php

return [
    'users' => [
        // Activation strategy with "email" or "manual"
        'activationStrategy' => 'email',
        'passwordHasher' => [
            'className' => 'Default'
        ],
        'auth' => [
            'loginRedirect' => [
                'plugin' => false,
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ],
            'logoutRedirect' => [
                'plugin' => 'users',
                'controller' => 'Users',
                'action' => 'login',
                'prefix' => false
            ],
            'unauthorizedRedirect' => '/'
        ]
    ]
];
