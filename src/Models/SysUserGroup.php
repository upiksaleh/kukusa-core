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
use Kukusa\Form\Field;

/**
 * Class SysUserGroup
 * @package Kukusa\Models
 * @property int $user_id [int(11)]
 * @property string $group [varchar(100)]
 * @property string $data_id [varchar(100)]
 * @property bool $active [tinyint(1)]
 * @property int $created_by [int(11)]
 * @property int $created_at [timestamp]
 * @property int $updated_by [int(11)]
 * @property int $updated_at [timestamp]
 */
class SysUserGroup extends BaseModelController
{
    public static function tableName()
    {
        return '{{%sys_users_group}}';
    }

    /**
     * Form fields
     * @return array
     */
    public function form_fields()
    {
        return [
            new Field(['name'=>'id'])
        ];
    }
    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new \Kukusa\Base\ModelOptions([
            'title' => Kukusa::t('kukusa','Grup Pengguna')
        ]);
    }
}