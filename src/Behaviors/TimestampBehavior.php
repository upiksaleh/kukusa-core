<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Behaviors;


use yii\db\Expression;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public function init()
    {
        parent::init();
        if(!$this->value) $this->value = new Expression('NOW()');
    }
}