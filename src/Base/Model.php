<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


class Model extends \yii\base\Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DEFAULT = self::SCENARIO_CREATE;
    use ModelTrait;
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}