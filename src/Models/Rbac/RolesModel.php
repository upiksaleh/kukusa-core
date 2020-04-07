<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models\Rbac;


use Kukusa\Base\BaseModelController;
use Kukusa\Base\ModelOptions;
use Kukusa\Form\Field;
use Kukusa\Rbac\DbManager;
use Kukusa\Rbac\RbacHelper;
use yii\helpers\StringHelper;

class RolesModel extends BaseModelController
{
    public static function tableName()
    {
        return (new DbManager())->itemTable;
    }

    public function rules()
    {
        return [
            [['name','type'],'required'],
            ['description', 'string']
        ];
    }

    public static function find()
    {
        return parent::find()->where(['type' => 1]);
    }

    /**
     * @inheritDoc
     */
    public function form_fields()
    {
        return [
            new Field(['name' => 'name']),
            new Field(['name' => 'description']),
            new Field(['name' => 'type','type' => Field::HIDDEN, 'attribute' => ['value'=>1]]),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new ModelOptions([
            'useLinkEncoded' => true,
            'gridActionColumn' => [
                'visibleButtons' => [
                    'delete' => function ($model, $key, $index) {
                        return StringHelper::startsWith($model->name, RbacHelper::$idRoleCustom);
                    },
                ]
            ]
        ]);

    }
}