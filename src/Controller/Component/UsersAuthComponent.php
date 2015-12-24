<?php

namespace Pie\Users\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;

/**
 * Users Auth Component
 *
 * @package Pie\Users\Controller\Component
 */
class UsersAuthComponent extends AuthComponent
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->config(
            [
                'authenticate' => [
                    'Form' => [
                        'scope' => ['Users.status' => 1],
                        'fields' => ['username' => 'email', 'password' => 'password']
                    ]
                ],
                'loginAction' => [
                    'plugin' => 'Pie/Users',
                    'controller' => 'Users',
                    'action' => 'login',
                    'prefix' => false
                ],
                'loginRedirect' => Configure::read('pie.users.auth.loginRedirect'),
                'logoutRedirect' => Configure::read('pie.users.auth.logoutRedirect'),
                'unauthorizedRedirect' => Configure::read('pie.users.auth.unauthorizedRedirect'),
                'authError' => Configure::read('pie.users.auth.authError'),
                'authorize' => ['Controller'],
                'flash' => Configure::read('pie.users.auth.flash')
            ]
        );

        parent::initialize($config);
        $this->config($config);
    }
}
