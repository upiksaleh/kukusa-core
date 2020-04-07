<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Helpers;


use Kukusa\Html\Grid;
use Kukusa\Helpers\ArrayHelper;

class Html extends \yii\helpers\Html
{

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function icon($name, $options = [])
    {

        $iconMap = [
            'data' => 'far fa-dashboard',
            'system' => 'far fa-gear',
            'menu-header' => 'far fa-folders',
            'menu-group' => 'far fa-folder',
            'menu-item' => 'far fa-file',
            'settings' => 'far fa-cogs',
            'setting' => 'far fa-cog',
            'roles-permissions' => 'far fa-user-lock',
            'reports' => 'far fa-file-invoice',
            'password' => 'far fa-key',
        ];
        if(isset($iconMap[$name])){
            $name = $iconMap[$name];
            $class = $name;
        }else{
            $classPrefix = ArrayHelper::remove($options, 'prefix', 'fa fa-');
            $class = $classPrefix.$name;
        }
        $tag = ArrayHelper::remove($options, 'tag', 'span');
        $size = ArrayHelper::remove($options, 'size', 0);
        if($size !== 0){
            $class .= ' fa-'.$size.'x';
        }
        static::addCssClass($options, $class);
        return static::tag($tag, '', $options);
    }
}