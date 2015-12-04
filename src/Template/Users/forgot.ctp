<div class="users form">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create($user, ['novalidate' => true]) ?>
    <fieldset>
        <legend><?= __d('Users', 'Please enter your email') ?></legend>
        <?= $this->Form->input('email') ?>
    </fieldset>
    <?= $this->Form->button(__d('Users', 'Resend')); ?>
    <?= $this->Form->end() ?>
</div>
