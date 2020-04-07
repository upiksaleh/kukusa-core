<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models;


use Kukusa\Base\BaseModelController;
use Kukusa\Base\ModelOptions;
use Kukusa\Behaviors\BlameableBehavior;
use Kukusa\Behaviors\TimestampBehavior;
use Kukusa\Form\Field;
use Kukusa\Log\LoggableBehavior;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * Class SysGroups
 * @package Kukusa\Models
 * @property string $name [varchar(32)]
 * @property string $class [varchar(255)]
 * @property int $created_by [int(11)]
 * @property int $created_at [timestamp]
 * @property int $updated_by [int(11)]
 * @property int $updated_at [timestamp]
 */
class SysGroups extends BaseModelController
{
    public static function tableName()
    {
        return '{{%sys_groups}}';
    }

    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            LoggableBehavior::class
        ];
    }


    public function rules()
    {
        return [
            [['name', 'class'], 'required'],
            ['name', 'unique'],
            ['name', 'string', 'max' => 32],
            ['class', 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritDoc
     */
    public function form_fields()
    {
        return [
            new Field(['name' => 'name']),
            new Field(['name' => 'class']),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new ModelOptions([
            'filterRules' => [
                ['name', 'string']
            ],
            'activeDataFilter' => [

            ],
            'dataProvider' => [
            ],
        ]);
    }
}