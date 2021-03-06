<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Filters;


class AccessControl extends \yii\filters\AccessControl
{
    /**
     * @inheritDoc
     */
    public $ruleConfig = ['class' => 'Kukusa\Filters\AccessRule'];
}