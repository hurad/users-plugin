<?php

namespace Pie\Users\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;

/**
 * App Controller
 *
 * @package Users\Controller
 */
class AppController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Cookie');
        $this->loadComponent(
            'Auth',
            [
                'authenticate' => [
                    'Form' => [
                        'scope' => ['Users.status' => 1],
                        'fields' => ['username' => 'email', 'password' => 'password']
                    ]
                ],
                'loginAction' => [
                    'controller' => 'Users',
                    'action' => 'login',
                    'prefix' => false
                ],
                'loginRedirect' => Configure::read('users.auth.loginRedirect'),
                'logoutAction' => [
                    'controller' => 'Users',
                    'action' => 'logout',
                    'prefix' => false
                ],
                'logoutRedirect' => Configure::read('users.auth.logoutRedirect'),
                'unauthorizedRedirect' => Configure::read('users.auth.unauthorizedRedirect'),
                'authorize' => ['Controller'],
                'flash' => ['element' => 'error']
            ]
        );
    }
}
