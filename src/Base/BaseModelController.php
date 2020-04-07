<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


use Kukusa;
use Kukusa\Db\ActiveRecord;
use Kukusa\Helpers\ArrayHelper;
use Kukusa\Helpers\Url;
use Kukusa\Rbac\RbacHelper;
use yii\base\InvalidConfigException;

/**
 * Class BaseModelController
 * @package Kukusa\Base
 */
abstract class BaseModelController extends ActiveRecord
{
    use BaseModelRepoControllerTrait;
    public function init()
    {
        parent::init();
        $this->init_base();
    }
}