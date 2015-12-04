<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create($user, ['novalidate' => true]) ?>

    <fieldset>
        <legend><?= __('Register') ?></legend>
        <?= $this->Form->input('username', ['placeholder' => __('Username')]) ?>
        <?= $this->Form->input('email', ['placeholder' => __('Email')]) ?>
        <?= $this->Form->input('password', ['placeholder' => __('Password')]) ?>
        <?= $this->Form->input('confirm_password', ['type' => 'password', ' placeholder' => __('Confirm password')]) ?>
        <?= $this->Form->input('details.first_name', [' placeholder' => __('First name')]) ?>
        <?= $this->Form->input('details.last_name', [' placeholder' => __('Last name')]) ?>
        <?= $this->Form->button(__('Sign up')) ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
