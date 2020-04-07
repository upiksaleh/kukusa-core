<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa\Helpers\ArrayHelper;

class View extends \yii\web\View
{
    public function params($key, $default = null)
    {
        return ArrayHelper::getValue($this->params, $key, $default);
    }
}