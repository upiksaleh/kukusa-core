<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Db\Migration;

class m200220_011100_sys_groups extends Migration
{
    protected $tableName = '{{%sys_groups}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'name' => $this->string(32)->notNull(),
            'class' => $this->string(255)->notNull(),
        ], $this->getTableOptions());
        $this->addPrimaryKey('pk-name', $this->tableName, 'name');
        $this->addForeignKey('fk-user', $this->tableName, 'user_id', '{{%sys_users}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx-user-group', $this->tableName, ['user_id', 'group'], true);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}