<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Console;


use Kukusa;

/**
 * Class Application
 * @package Kukusa\Console
 *
 * @property \Kukusa\Base\ModelRepo $modelRepo
 */
class Application extends \yii\console\Application
{
    public function __construct($config = [])
    {
        Kukusa::$app = $this;
        parent::__construct($config);
    }
}