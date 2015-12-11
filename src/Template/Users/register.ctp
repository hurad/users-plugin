<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create($user, ['novalidate' => true]) ?>

    <fieldset>
        <legend><?= __d('users', 'Register') ?></legend>
        <?= $this->Form->input('username', ['placeholder' => __d('users', 'Username')]) ?>
        <?= $this->Form->input('email', ['placeholder' => __d('users', 'Email')]) ?>
        <?= $this->Form->input('password', ['placeholder' => __d('users', 'Password')]) ?>
        <?= $this->Form->input('confirm_password', ['type' => 'password', ' placeholder' => __d('users', 'Confirm password')]) ?>
        <?= $this->Form->input('details.first_name', [' placeholder' => __d('users', 'First name')]) ?>
        <?= $this->Form->input('details.last_name', [' placeholder' => __d('users', 'Last name')]) ?>
        <?= $this->Form->button(__d('users', 'Sign up')) ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
