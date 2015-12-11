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
    }
}
