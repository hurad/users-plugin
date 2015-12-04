<?php

namespace Pie\Users\Model\Table;

use Cake\ORM\Table;

/**
 * Class UsersTable
 *
 * @package Users\Model\Table
 */
class UsersTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->addBehavior(
            'Timestamp',
            [
                'events' => [
                    'Model.beforeSave' => [
                        'created_at' => 'new',
                        'updated_at' => 'always',
                    ]
                ]
            ]
        );

        $this->hasMany(
            'UserDetails',
            [
                'className' => 'Users.UserDetails',
                'foreignKey' => 'user_id',
                'dependent' => true,
                'propertyName' => 'details'
            ]
        );
    }
}
