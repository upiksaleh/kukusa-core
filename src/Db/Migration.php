<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Db;


class Migration extends \yii\db\Migration
{
    protected $tableOptions = [];
    protected $useColumnInfo = true;
    public function init()
    {
        parent::init();
        $this->mySQL_UTF8_unicode_InnoDB();
    }

    /**
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }

    protected function isOracle()
    {
        return $this->db->driverName === 'oci';
    }
    protected function isMySQL(){
        return $this->db->driverName === 'mysql';
    }

    /**
     * definisikan pilihan untuk database mysql, bahwa memakai engine InnoDB dan set utf8_unicode_ci
     * // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
     */
    protected function mySQL_UTF8_unicode_InnoDB(){
        if($this->isMySQL()){
            $this->tableOptions[] = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

    protected function buildFkClause($delete = '', $update = '')
    {
        if ($this->isMSSQL()) {
            return '';
        }

        if ($this->isOracle()) {
            return ' ' . $delete;
        }

        return implode(' ', ['', $delete, $update]);
    }
    protected function getTableOptions(){
        return implode(" ", $this->tableOptions);
    }

    protected function columnInfo()
    {

    }
    public function createTable($table, $columns, $options = null)
    {
        if($this->useColumnInfo) {
            $columns['created_by'] = $this->integer();
            $columns['created_at'] = $this->timestamp();
            $columns['updated_by'] = $this->integer();
            $columns['updated_at'] = $this->timestamp();
        }
        parent::createTable($table, $columns, $options);
    }

}