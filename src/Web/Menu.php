<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa;
use Kukusa\Rbac\RbacHelper;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

class Menu extends BaseObject
{
    const TYPE_HEADER = 1;
    const TYPE_GROUP = 2;
    const TYPE_MENU = 3;

    public $id;
    public $type = self::TYPE_MENU;
    public $label;
    public $encodeLabel = true;
    public $route;
    /**
     * @var bool
     */
    public $isModelRepo = false;
    /**
     * @var array|Menu[]
     */
    public $children = [];
    /**
     * @var int
     */
    public $pos = 0;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var bool
     */
    public $isActive = false;
    /**
     * @var bool|string
     */
    public $activeRoute = false;

    public $permissions = [];

    public $options = [];

    public function init()
    {
        if (!$this->id)
            throw new InvalidConfigException("'id' untuk menu diperlukan");
        if (!$this->label)
            $this->label = Inflector::camel2words($this->id);
        if (is_string($this->route))
            $this->route = [$this->route];

        foreach ($this->children as $name => $child) {
            if (is_array($child)) {
                $child = new Menu($child);
                $this->children[$name] = $child;
            }
        }
        if (!$this->icon) {
            if ($this->type === Menu::TYPE_GROUP) $this->icon = 'menu-group';
            elseif ($this->type === Menu::TYPE_MENU) $this->icon = 'menu-item';
            elseif ($this->type === Menu::TYPE_HEADER) $this->icon = 'menu-header';
        }
        if (is_array($this->permissions) && empty($this->permissions) && !empty($this->route)) {
            $this->permissions[] = RbacHelper::menuRoleName($this->route[0]);
        }
        if ($this->isModelRepo) {
            $this->activeRoute = Kukusa::$app->modelRepo->prefix_url($this->route[0]);
        }
        if ($this->activeRoute === true) {
            $this->activeRoute = $this->route[0];
        }
    }

    public function getRouteBuild()
    {
        $route = $this->route;
        if (!$route) return [];
        if ($this->isModelRepo) {
            $_route = Kukusa::$app->modelRepo->_to_url($this->route[0]);
            $route[0] = '/' . ltrim($_route[0], '/');
        }
        return $route;
    }
}