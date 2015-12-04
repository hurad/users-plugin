<?php

namespace Users\Controller;

use Cake\Auth\PasswordHasherFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Validation\Validator;
use Users\Model\Entity\User;
use Users\Model\Entity\UserDetail;
use Users\Model\Table\UserDetailsTable;
use Users\Model\Table\UsersTable;

/**
 * Users Controller
 *
 * @package Users\Controller
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['register', 'login', 'logout', 'verify', 'resendActivationEmail', 'forgot', 'reset']);
    }

    /**
     * User login
     */
    public function login()
    {
        $this->set('title', __d('users', 'Login'));

        if ($this->Auth->user()) {
            $this->Flash->set(__d('users', 'You already logged in.'));

            return $this->redirect($this->Auth->config('loginRedirect'));
        } else {
            if ($this->request->is('post')) {
                if ($user = $this->Auth->identify()) {
                    $this->Auth->setUser($user);
                    $this->request->session()->delete('Message.auth');

                    if (!$this->request->data('remember_me')) {
                        $this->Cookie->delete('Auth.User');
                    } else {
                        $cookie = [];
                        $cookie['email'] = $this->request->data('email');
                        $cookie['password'] = $this->request->data('password');
                        $this->Cookie->write('Auth.User', $cookie);
                    }

                    $this->Flash->set(__d('users', 'You have successfully logged in'), ['element' => 'success']);

                    return $this->redirect($this->Auth->redirectUrl());
                }
                $this->Flash->set(
                    __d('users', 'Your email or password was incorrect or your account is inactive.'),
                    ['element' => 'error']
                );
            }
        }
    }

    /**
     * User logout
     */
    public function logout()
    {
        if ($this->Auth->user()) {
            $this->request->session()->destroy();
            $this->Cookie->delete('Auth.User');
            $this->Flash->set(__d('users', 'You are successfully logged out'), ['element' => 'success']);
            $this->redirect($this->Auth->logout());
        } else {
            $this->Flash->set(__d('users', 'You already logged out.'), ['element' => 'error']);
            $this->redirect($this->request->referer());
        }
    }

    /**
     * User register
     *
     * @return \Cake\Network\Response|void
     */
    public function register()
    {
        $this->set('title', __d('users', 'Register'));
        /** @var $usersTable UsersTable */
        $usersTable = TableRegistry::get('Users.Users');

        /** @var User $user */
        $user = $usersTable->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $validator = new Validator();
            $validator->provider('table', $usersTable)
                ->add(
                    'email',
                    [
                        'uniqueEmail' => [
                            'rule' => 'validateUnique',
                            'message' => __d('users', 'This email has been taken.'),
                            'provider' => 'table'
                        ],
                        'validEmail' => [
                            'rule' => 'email',
                            'message' => __d('users', 'Please enter a valid email address.')
                        ]
                    ]
                )
                ->add(
                    'password',
                    [
                        'minLengthPassword' => [
                            'rule' => ['minLength', 8],
                            'message' => __d('users', 'Minimum length of password is 8 characters.')
                        ]
                    ]
                )
                ->add(
                    'confirm_password',
                    [
                        'equalToPassword' => [
                            'rule' => ['compareWith', 'password'],
                            'message' => __d('users', 'Entered passwords do not match.')
                        ]
                    ]
                );

            $errors = $validator->errors($this->request->data);
            $user->errors($errors);

            if (empty($errors)) {
                $detailsData = [];
                $emailData = [];

                $user->set('username', $this->request->data('username'))
                    ->set('email', $this->request->data('email'))
                    ->set(
                        'password',
                        PasswordHasherFactory::build(Configure::read('users.passwordHasher'))
                            ->hash($this->request->data('password'))
                    )
                    ->set('status', 0);

                if ('email' === Configure::read('users.activationStrategy')) {
                    $activationKey = Security::hash(microtime(true), 'sha256', true);
                    $activationUrl = Router::url([
                        '_full' => true,
                        'plugin' => 'Users',
                        'controller' => 'Users',
                        'action' => 'verify',
                        $activationKey
                    ]);

                    $emailData['activationKey'] = $activationKey;
                    $emailData['activationUrl'] = $activationUrl;

                    $detailsData[] = new UserDetail([
                        'key' => 'activation_key',
                        'value' => $activationKey
                    ]);
                }

                if (!empty($this->request->data('details')) && is_array($this->request->data('details'))) {
                    foreach ($this->request->data('details') as $detailKey => $detailValue) {
                        $detailsData[] = new UserDetail([
                            'key' => $detailKey,
                            'value' => $detailValue
                        ]);
                    }

                    $user->set('details', $detailsData);
                }

                if ($usersTable->save($user)) {
                    $this->getMailer('Users.User')
                        ->set($emailData)
                        ->send('welcome', [$user]);

                    $this->Flash->set(
                        __d('users', 'Congratulations, You are successfully registered'),
                        ['element' => 'success']
                    );

                    return $this->redirect('/');
                }
            }

            $this->Flash->set(__d('users', 'The user could not be saved. Please, try again.'), ['element' => 'error']);
        }

        $this->set('user', $user);
    }

    /**
     * Verify user
     *
     * @param null|string $key Activation hash
     *
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function verify($key = null)
    {
        /** @var $usersTable UsersTable */
        $usersTable = TableRegistry::get('Users.Users');

        /** @var $user User */
        $user = $usersTable->find()
            ->where(['status' => 0])
            ->matching(
                'UserDetails',
                function (Query $query) use ($key) {
                    return $query->where(['UserDetails.key' => 'activation_key', 'UserDetails.value' => $key]);
                }
            )->contain('UserDetails')
            ->first();

        if (is_null($user)) {
            throw new NotFoundException();
        }

        $user->set('status', 1);
        $usersTable->connection()->begin();

        try {
            if ($usersTable->save($user)) {
                /** @var $userDetailsTable UserDetailsTable */
                $userDetailsTable = TableRegistry::get('Users.UserDetails');
                $userDetailsTable->delete($user->get('_matchingData')['UserDetails']);
                $usersTable->connection()->commit();

                $this->Flash->set(__d('users', 'Your account is confirmed.'), ['element' => 'success']);

                return $this->redirect('/');
            }
        } catch (\Exception $e) {
            $usersTable->connection()->rollback();
            throw $e;
        }

        $this->Flash->set(__d('users', 'Cannot confirm your email. Please, try again.'), ['element' => 'error']);
    }

    /**
     * Resend activation email
     *
     * @return \Cake\Network\Response|null
     */
    public function resendActivationEmail()
    {
        $this->set('title', __d('users', 'Resend activation email'));

        /** @var $usersTable UsersTable */
        $usersTable = TableRegistry::get('Users.Users');

        /** @var User $user */
        $user = $usersTable->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $validator = new Validator();
            $validator->add(
                'email',
                [
                    'validEmail' => [
                        'rule' => 'email',
                        'message' => __d('users', 'Please enter a valid email address.')
                    ]
                ]
            );

            $errors = $validator->errors($this->request->data);
            $user->errors($errors);

            if (empty($errors)) {
                /** @var $user User */
                $user = $usersTable->find()
                    ->where(['status' => 0, 'email' => $this->request->data('email')])
                    ->contain('UserDetails')
                    ->first();

                if (is_null($user)) {
                    $this->Flash->set(__d('users', 'Your not registered or account is activate.'),
                        ['element' => 'error']);

                    return $this->redirect($this->request->referer());
                }

                $activationKey = Security::hash(microtime(true), 'sha256', true);

                $done = false;
                if (array_key_exists('activation_key', $user->getDetails())) {
                    $userDetailsTable = TableRegistry::get('Users.UserDetails');
                    if ($userDetailsTable->updateAll(
                        ['value' => $activationKey],
                        ['id' => $user->getDetails()['activation_key']->id]
                    )
                    ) {
                        $done = true;
                    }
                } else {
                    $user->set(
                        'details',
                        [
                            new UserDetail([
                                'user_id' => $user->get('id'),
                                'key' => 'activation_key',
                                'value' => $activationKey
                            ])
                        ]
                    );

                    if ($usersTable->save($user)) {
                        $done = true;
                    }
                }

                if ($done) {
                    $this->getMailer('Users.User')
                        ->set(
                            [
                                'activationKey' => $activationKey,
                                'activationUrl' => Router::url([
                                    '_full' => true,
                                    'plugin' => 'Users',
                                    'controller' => 'Users',
                                    'action' => 'verify',
                                    $activationKey
                                ])
                            ]
                        )
                        ->send('activation', [$user]);

                    $this->Flash->set(
                        __d('users', 'Resend activation email successfully.'),
                        ['element' => 'success']
                    );

                    return $this->redirect($this->referer());
                }
            }
        }

        $this->set(['user' => $user]);
    }

    public function forgot()
    {
        $this->set('title', __d('users', 'Forgot password'));

        /** @var $usersTable UsersTable */
        $usersTable = TableRegistry::get('Users.Users');

        /** @var User $user */
        $user = $usersTable->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $validator = new Validator();
            $validator->add(
                'email',
                [
                    'validEmail' => [
                        'rule' => 'email',
                        'message' => __d('users', 'Please enter a valid email address.')
                    ]
                ]
            );

            $errors = $validator->errors($this->request->data);
            $user->errors($errors);

            if (empty($errors)) {
                /** @var $user User */
                $user = $usersTable->find()
                    ->where(['status' => 1, 'email' => $this->request->data('email')])
                    ->contain('UserDetails')
                    ->first();

                if (is_null($user)) {
                    $this->Flash->set(__d('users', 'Your not registered or account is deactivate.'),
                        ['element' => 'error']);

                    return $this->redirect($this->request->referer());
                }

                $resetKey = Security::hash(microtime(true), 'sha256', true);

                $done = false;
                if (array_key_exists('reset_key', $user->getDetails())) {
                    $userDetailsTable = TableRegistry::get('Users.UserDetails');
                    if ($userDetailsTable->updateAll(
                        ['value' => $resetKey],
                        ['id' => $user->getDetails()['reset_key']->id]
                    )
                    ) {
                        $done = true;
                    }
                } else {
                    $user->set(
                        'details',
                        [
                            new UserDetail([
                                'user_id' => $user->get('id'),
                                'key' => 'reset_key',
                                'value' => $resetKey
                            ])
                        ]
                    );

                    if ($usersTable->save($user)) {
                        $done = true;
                    }
                }

                if ($done) {
                    $this->getMailer('Users.User')
                        ->set(
                            [
                                'resetKey' => $resetKey,
                                'resetUrl' => Router::url([
                                    '_full' => true,
                                    'plugin' => 'Users',
                                    'controller' => 'Users',
                                    'action' => 'reset',
                                    $resetKey
                                ])
                            ]
                        )
                        ->send('forgot', [$user]);

                    $this->Flash->set(
                        __d('users', 'An email has been sent with instructions to reset your password.'),
                        ['element' => 'success']
                    );

                    return $this->redirect($this->referer());
                }
            }
            $this->Flash->set(__('Error occurred. Please, try again.'), ['element' => 'error']);
        }

        $this->set(['user' => $user]);
    }

    /**
     * Reset password
     *
     * @param null|string $key
     *
     * @return \Cake\Network\Response|void
     */
    public function reset($key = null)
    {
        $this->set('title', __('Reset Password'));

        /** @var $usersTable UsersTable */
        $usersTable = TableRegistry::get('Users.Users');

        /** @var $user User */
        $user = $usersTable->find()
            ->where(['status' => 1])
            ->matching(
                'UserDetails',
                function (Query $query) use ($key) {
                    return $query->where(['key' => 'reset_key', 'value' => $key]);
                }
            )
            ->contain('UserDetails')
            ->first();

        if (!$user || is_null($key)) {
            throw new NotFoundException();
        }

        if ($this->request->is(['post', 'put'])) {
            $validator = new Validator();
            $validator->add(
                'new_password',
                [
                    'minLengthPassword' => [
                        'rule' => ['minLength', 8],
                        'message' => __d('users', 'Minimum length of password is 8 characters.')
                    ],
                ]
            )->add(
                'confirm_password',
                [
                    'equalToPassword' => [
                        'rule' =>
                            function ($value, $context) {
                                if ($value === $context['data']['new_password']) {
                                    return true;
                                } else {
                                    return false;
                                }
                            },
                        'message' => __d('users', 'Entered passwords do not match.')
                    ],
                ]
            );

            $errors = $validator->errors($this->request->data, $user->isNew());
            $user->errors($errors);

            if (empty($errors)) {
                $user->set(
                    'password',
                    PasswordHasherFactory::build(Configure::read('users.passwordHasher'))
                        ->hash($this->request->data('new_password'))
                );

                if ($usersTable->save($user)) {
                    /** @var $userDetailsTable UserDetailsTable */
                    $userDetailsTable = TableRegistry::get('Users.UserDetails');
                    $userDetailsTable->delete($user->getDetails()['reset_key']);

                    $this->Flash->set(__('Your password has been reset successfully.'), ['element' => 'success']);
                    return $this->redirect(['action' => 'login']);
                }
            }
            $this->Flash->set(__('An error occurred. Please try again.'), ['element' => 'error']);
        }

        $this->set(compact('user'));
    }
}
