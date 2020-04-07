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
use Kukusa\Rbac\DbManager;

class Permissions extends BaseModelController
{

    public static function tableName()
    {
        return (new DbManager())->itemTable;
    }
    public static function find()
    {
        return parent::find()->where(['type'=>2]);
    }

    /**
     * @inheritDoc
     */
    public function form_fields()
    {
        // TODO: Implement form_fields() method.
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        // TODO: Implement _options() method.
    }
}