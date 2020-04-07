<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Db;


use Kukusa\Base\Model;
use Kukusa\Base\ModelTrait;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = Model::SCENARIO_CREATE;
    const SCENARIO_UPDATE = Model::SCENARIO_UPDATE;

    use ModelTrait;

}