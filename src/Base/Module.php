<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


use Kukusa\Web\Menu;
use Kukusa\Web\WebMenu;
use yii\base\BootstrapInterface;

abstract class Module extends \yii\base\Module implements BootstrapInterface, ModuleInterface
{
    public $requiredModule = [];

    public $dashboardWidgetClass;

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {

    }

    public function init()
    {
        if ($this->controllerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->controllerNamespace = substr($class, 0, $pos) . '\\Controllers';
            }
        }
        parent::init();
    }
    public function boot_all($app)
    {

    }

    public function boot_console($app)
    {

    }
    public function boot_web($app)
    {
        $this->web_menu_init($app->webMenu);
    }

    /**
     * @param WebMenu $menu
     */
    protected function web_menu_init($menu){

    }
    abstract protected function onSetup();

    public function setup_module()
    {
        $this->onSetup();
    }
}