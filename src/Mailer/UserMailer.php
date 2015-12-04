<?php

namespace Pie\Users\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Pie\Users\Model\Entity\User;

/**
 * User Mailer
 *
 * @package Users\Mailer
 */
class UserMailer extends Mailer
{
    /**
     * @param User $user
     */
    public function welcome(User $user)
    {
        $this->from(Configure::read('users.emailFrom'))
            ->to($user->get('email'))
            ->subject(__('Welcome "{0}"', $user->get('username')))
            ->template('Users.register')
            ->emailFormat(Email::MESSAGE_HTML)
            ->set(
                [
                    'email' => $user->get('email')
                ]
            );
    }

    public function activation(User $user)
    {
        $this->from(Configure::read('users.emailFrom'))
            ->to($user->get('email'))
            ->subject(__('Activation account'))
            ->template('Users.activation')
            ->emailFormat(Email::MESSAGE_HTML)
            ->set(
                [
                    'user' => $user,
                    'email' => $user->get('email')
                ]
            );
    }

    public function forgot(User $user)
    {
        $this->from(Configure::read('users.emailFrom'))
            ->to($user->get('email'))
            ->subject(__('Reset Password'))
            ->template('Users.forgot')
            ->emailFormat(Email::MESSAGE_HTML)
            ->set(
                [
                    'user' => $user,
                ]
            );
    }
}
