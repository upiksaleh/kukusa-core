<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


use Kukusa;
use yii\base\Component;
use Kukusa\Helpers\ArrayHelper;
use yii\helpers\StringHelper;

class Config extends Component
{
    private $_config;
    public $files = [];

    public function init()
    {
        foreach ($this->files as $key => $file){
            $file = Kukusa::getAlias($file);
            if(!is_file($file)) continue;
            $this->_config[$key] = require $file;
        }
    }

    public function get($key, $default = null)
    {
        if(StringHelper::startsWith($key, 'app.'))
            return ArrayHelper::getValue(Kukusa::$app, substr($key, 4, strlen($key)));

        return ArrayHelper::getValue($this->_config, $key, $default);
    }
}