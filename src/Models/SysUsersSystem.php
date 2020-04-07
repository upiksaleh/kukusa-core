<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models;


use Kukusa;
use Kukusa\Base\BaseUser;
use Kukusa\Form\Field;

/**
 * Class SysUsersSystem
 * @package Kukusa\Models
 * @property int $id [int(11)]
 * @property string $fullname [varchar(100)]
 * @property int $created_by [int(11)]
 * @property int $created_at [timestamp]
 * @property int $updated_by [int(11)]
 * @property int $updated_at [timestamp]
 *
 * @property SysUser $sysuser
 */
class SysUsersSystem extends BaseUser
{
    public static function tableName()
    {
        return '{{%sys_users_system}}';
    }


    // --------------------------------------------------------------------
    public function getSysuser()
    {
        return $this->hasOne(SysUser::class, ['data' => 'id']);
    }
    // --------------------------------------------------------------------

    /**
     * Form fields
     * @return array
     */
    protected function _form_fields()
    {
        return [
            new Field(['name' => 'fullname', 'attribute' => ['disabled' => true], 'activeField' => [
                'options' => [
                    'ada' => 'askdjals'
                ]
            ]]),
            new Field(['name' => 'created_by', 'type'=>Field::PASSWORD]),
//            new Kukusa\Form\Field(['name' => 'fullNamae']),
        ];
    }

    /**
     * full name of user
     * @return string
     */
    public function getFullName()
    {
        return $this->fullname;
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new \Kukusa\Base\ModelOptions([
            'title' => Kukusa::t('kukusa', 'Sys Users')
        ]);
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param Kukusa\Base\FilterModel|void $filterModel
     */
    protected function onSearch(&$query, $filterModel)
    {
    }
}