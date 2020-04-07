<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models;


use Kukusa;
use Kukusa\Base\BaseModelController;
use Kukusa\Base\FilterModel;

/**
 * Class SysUser
 * @package Kukusa\Models
 * @property int $id [int(11)]
 * @property string $username [varchar(64)]
 * @property string $email [varchar(100)]
 * @property string $password [varchar(64)]
 * @property string $group [varchar(32)]
 * @property int $data_id [int(11)]
 * @property int $avatar [int(11)]
 * @property bool $status [tinyint(1)]
 * @property string $access_token [varchar(32)]
 * @property string $auth_key [varchar(32)]
 * @property string $reset_token [varchar(64)]
 * @property int $last_time [int(11)]
 * @property int $created_by [int(11)]
 * @property int $created_at [timestamp]
 * @property int $updated_by [int(11)]
 * @property int $updated_at [timestamp]
 *
 *
 * @property string $groupClass
 * @property Kukusa\Base\BaseUser $data
 */
class SysUser extends BaseModelController
{

    const STATUS_ACTIVE = 1;
    const STATUS_NON_ACTIVE = 0;
    const STATUS_DELETED = -1;

    public static function tableName()
    {
        return '{{%sys_users}}';
    }

    // GETTER -------------------------------------------------------------
    public function getData()
    {
        return $this->hasOne($this->groupClass, ['id' => 'data_id']);
    }

    public function getGroupClass()
    {
        return Kukusa::$app->user->groupClass($this->group);
    }
    // END GETTER--------------------------------------------------------------------

    protected function _form_fields()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new Kukusa\Base\ModelOptions([
            'title' => Kukusa::t('kukusa','Sys Users'),
            'gridView' => [
                'columns' => [
                    'id',
                    'username',
                    'group',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by'
                ]
            ],
            'filterRules' => [
                [['username','group'],'safe']
            ]
        ]);
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param FilterModel|static $filterModel
     */
    protected function onSearch(&$query, $filterModel)
    {
        if($filterModel->username !== '')
            $query->andWhere(['like','username',$filterModel->username]);
    }
}