<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

class m200220_011200_sys_users_group extends \Kukusa\Db\Migration
{
    public $tableName = '{{%sys_users_group}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'user_id' => $this->integer()->notNull(),
            'group' => $this->string(100)->notNull(),
            'data_id' => $this->string(100)->notNull(),
            'active' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
        ], $this->getTableOptions());
        $this->addForeignKey('fk-user', $this->tableName, 'user_id', '{{%sys_users}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx-user-group', $this->tableName, ['user_id', 'group'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}