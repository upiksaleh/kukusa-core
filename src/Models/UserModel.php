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
use yii\rest\IndexAction;

class UserModael extends BaseModelController
{
    public static $modelId = 'system-user';

    public static function tableName()
    {
        return '{{%user}}';
    }
    public function rules()
    {
        return [
            [['nama', 'data'], 'required'],
//            [['nama', 'data'], 'in','range' => [1,2]],
//            ['nama', 'default', 'value' => '\asa', 'on' => self::SCENARIO_CREATE],
//            ['namaa', 'default', 'value' => '\asa', 'on' => self::SCENARIO_CREATE],
//            ['nama', 'exist', 'targetClass' => UserModel::class, 'targetAttribute' => ['a'=>'n']],
        ];
    }

    public static function customApiRoute()
    {
        return [
            'GET a' => 'index'
         ];
    }

    public static function actionDa($a, $b)
    {
        return [$a=>$b];
    }

    public static function actions()
    {
        return [
            'wa'=> [
                'class' => IndexAction::class,
                'modelClass' => static::class,
                'id'=>1
            ]
        ];
    }

    /**
     * Form fields
     * @return array
     */

    public function form_fields()
    {
        return [
            new Field(['name' => 'nama', 'type' => Field::TEXT]),
            new Field(['name' => 'data']),

//            [['nama','data'], static::FORM_FIELDS_STRING, [
//                'a' => 'da'
//            ]],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new Kukusa\Base\ModelOptions([
            'title' => Kukusa::t('yihai','Sys Users')
        ]);
    }
}