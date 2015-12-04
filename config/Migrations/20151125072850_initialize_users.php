<?php

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table\ForeignKey;
use Phinx\Migration\AbstractMigration;

class InitializeUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('users')
            ->addColumn('username', AdapterInterface::PHINX_TYPE_STRING, ['limit' => 255, 'null' => true])
            ->addColumn('password', AdapterInterface::PHINX_TYPE_STRING, ['limit' => 255, 'null' => false])
            ->addColumn('email', AdapterInterface::PHINX_TYPE_STRING, ['null' => false])
            ->addColumn(
                'role',
                AdapterInterface::PHINX_TYPE_INTEGER,
                [
                    'limit' => 1,
                    'signed' => false,
                    'default' => 0,
                    'comment' => 'user, administrator',
                    'null' => false
                ]
            )
            ->addColumn(
                'status',
                AdapterInterface::PHINX_TYPE_INTEGER,
                [
                    'limit' => 1,
                    'signed' => false,
                    'default' => 1,
                    'comment' => 'banned, inactive, active',
                    'null' => false
                ]
            )
            ->addColumn('created_at', AdapterInterface::PHINX_TYPE_DATETIME, ['null' => false])
            ->addColumn('updated_at', AdapterInterface::PHINX_TYPE_DATETIME, ['null' => false])
            ->addIndex('username', ['unique' => true])
            ->addIndex('email', ['unique' => true])
            ->addIndex(['username', 'password', 'status'])
            ->addIndex(['email', 'password', 'status'])
            ->addIndex('status')
            ->addIndex('role')
            ->create();

        $this->table('user_details')
            ->addColumn('user_id', AdapterInterface::PHINX_TYPE_BIG_INTEGER, ['null' => false])
            ->addColumn('key', AdapterInterface::PHINX_TYPE_STRING, ['null' => false])
            ->addColumn('value', AdapterInterface::PHINX_TYPE_TEXT)
            ->addIndex(['key', 'user_id'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => ForeignKey::CASCADE, 'update' => ForeignKey::NO_ACTION])
            ->create();
    }
}
