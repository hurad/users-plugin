<?php

return [
    'pie' => [
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
                    'plugin' => 'Pie/Users',
                    'controller' => 'Users',
                    'action' => 'login',
                    'prefix' => false
                ],
                'unauthorizedRedirect' => '/',
                'authError' => __d('users', 'You are not authorized to access that location.'),
                'flash' => ['element' => 'error']
            ]
        ]
    ]
];
