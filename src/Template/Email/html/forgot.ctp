<?= __d('users', 'Someone wants to reset your password for "<b>{0}</b>"', $user->username) ?>
<br><br>
<?= __d('users', 'Please click on the following link to reset your password: {0}', $this->Html->link(__d('users', 'Reset password'), $resetUrl)) ?>