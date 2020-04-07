<?php

use Kukusa\Db\Migration;

class m200220_011080_activity_log extends Migration
{
    protected $useColumnInfo = false;
    public $tableName = '{{%sys_activity_logs}}';

    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'action' => $this->string(100)->notNull(),
            'model' => $this->string(100),
            'type' => $this->string(20)->notNull(),
            'user' => $this->string(64)->notNull(),
            'time' => $this->integer()->notNull(),
            'ip' => $this->string(45),
            'msg' => $this->binary(),

        ], $this->getTableOptions());
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}