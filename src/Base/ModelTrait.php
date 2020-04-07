<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


trait ModelTrait
{

    private $_extra_scenarios = [];

    public function scenarios()
    {
        return array_merge($this->_extra_scenarios, parent::scenarios());
    }

    /**
     * @param $name
     * @param array $attributes
     */
    public function addScenario($name, $attributes = [])
    {
        $scenarios = parent::scenarios();
        if (empty($attributes)) {
            if (isset($scenarios[$name]))
                $attributes = $scenarios[$name];
            elseif (isset($scenarios[self::SCENARIO_DEFAULT])) {
                $attributes = $scenarios[self::SCENARIO_DEFAULT];
            }
        }
        $this->_extra_scenarios[$name] = $attributes;
    }

    public static function searchClassName()
    {
        return self::classNameSort().'Search';
    }

    public static function classNameSort()
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

}