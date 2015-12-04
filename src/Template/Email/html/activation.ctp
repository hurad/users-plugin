<?= __d('users', 'Activation account') ?>
    <br><br>
<?= __d('users', 'Please click on the following link to activate your account: {0}', $this->Html->link(__d('users', 'Activation link'), $activationUrl)) ?>