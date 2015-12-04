<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create($user, ['novalidate' => true]) ?>

    <fieldset>
        <legend><?= __('Reset password') ?></legend>
        <?= $this->Form->input('new_password', ['type' => 'password', 'placeholder' => __('New password')]) ?>
        <?= $this->Form->input('confirm_password', ['type' => 'password', ' placeholder' => __('Confirm password')]) ?>
        <?= $this->Form->button(__('Reset')) ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
