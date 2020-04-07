<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Theme;


use Kukusa;
use yii\base\InvalidConfigException;
use yii\base\Theme;

class ThemeComponent extends \yii\base\Component
{
    public $list = [];
    public $active = false;

    public $pathMap = [];
    /** @var BaseTheme */
    public $activeTheme;

    private $path = [];

    private $_theme = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        foreach ($this->list as $name => $class) {
            if(is_string($class)) {
                if (!class_exists($class)) continue;
                /** @var BaseTheme $classObj */
                $classObj = new $class();
            }elseif(is_array($class)){
                $classObj = Kukusa::createObject($class);
            }else{
                throw new InvalidConfigException('Theme list item must array or string');
            }
            if (!$classObj instanceof BaseTheme)
                throw new InvalidConfigException("Theme list class is not instance of ThemeInterface");
            $this->initialize_theme($name, $classObj);
        }
    }

    public function bootstrap()
    {
        if($this->active === false) return;
        $this->initialize_active();
    }

    /**
     * @param string $name
     * @param BaseTheme $classObj
     */
    private function initialize_theme($name, $classObj)
    {
        Kukusa::setAlias('@kukusa-theme-'.$name, $classObj->getPath());
        $this->path[$name] = [
            'views' => $classObj->getPath() . '/views'
        ];
        $this->_theme[$name] = $classObj;
    }

    public function initialize_active()
    {
        $theme = $this->getActiveClass();
        $this->activeTheme = $theme;
        // container
        foreach ($theme->getContainer() as $class => $definition) {
            Kukusa::$container->set($class, $definition);
        }
        Kukusa::setAlias('@kukusa-active-theme', $theme->getPath());
        if(!Kukusa::$app->view->theme)
            Kukusa::$app->view->theme = Kukusa::createObject(Theme::class);
        Kukusa::$app->view->theme->setBasePath($theme->getPath());
        Kukusa::$app->view->theme->setBaseUrl('@web/themes/' . $this->active);
        $pathMap = [];
//        $pathMap['@yii/views'] = $pathMap['@kukusa/views'] = [
//            '@kukusa-active-theme/views',
//            '@kukusa-theme-default/views',
//        ];
        $pathMap = array_merge($this->pathMap, $pathMap);
        $pathMap = array_merge($pathMap, $theme->getPathMap());
        Kukusa::$app->view->theme->pathMap = $pathMap;

    }

    public function activeAlias()
    {
        return '@kukusa-theme-' . $this->active;
    }

    /**
     * @param string $active theme name
     */
    public function set($active = '')
    {
        if ($active == '')
            return;
        $this->active = $active;
        $this->initialize_active();
    }

    /**
     * @return ThemeInterface
     */
    public function getActiveClass()
    {

        return $this->_theme[$this->active];
    }

    public function pathView($view)
    {
        $path = $this->path[$this->active];
        return $path['views'] . '/' . $view;
    }
}