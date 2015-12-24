<?php

namespace Pie\Users\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Pie\Users\Controller\Component\UsersAuthComponent;

/**
 * App Controller
 *
 * @property UsersAuthComponent $UsersAuth
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
        $this->loadComponent('Pie/Users.UsersAuth');
    }
}
