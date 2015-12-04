<div class="users form">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create(null, ['novalidate' => true]) ?>
    <fieldset>
        <legend><?= __d('Users', 'Please enter your username and password') ?></legend>
        <?= $this->Form->input('email') ?>
        <?= $this->Form->input('password') ?>
    </fieldset>
    <?= $this->Form->button(__d('Users', 'Login')); ?>
    <?= $this->Form->end() ?>
</div>
