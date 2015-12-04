<?php

namespace Users\Model\Table;

use Cake\ORM\Table;

/**
 * UserDetails Table
 *
 * @package Users\Model\Table
 */
class UserDetailsTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->belongsTo(
            'Users',
            [
                'className' => 'Users.User',
                'foreignKey' => 'user_id',
                'propertyName' => 'user'
            ]
        );
    }
}
