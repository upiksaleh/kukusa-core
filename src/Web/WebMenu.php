<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa\Helpers\ArrayHelper;

/**
 * Class WebMenu
 * @package Kukusa\Web
 * @property array $lists
 * @property array $menus
 */
class WebMenu extends \yii\base\Component
{
    private $_allMenu = [];
    private $_lists = [
        'backend' => [
            'id' => 'backend',
            'children' => [],
            'type' => Menu::TYPE_HEADER
        ],
        'frontend' => [
            'id' => 'frontend',
            'children' => [],
            'type' => Menu::TYPE_HEADER
        ],
        'modules' => [
            'id' => 'modules',
            'children' => [],
            'type' => Menu::TYPE_HEADER
        ]
    ];

    public function init()
    {

    }

    /**
     * @param string|array $parent
     * @param array|Menu $menu
     * @return WebMenu
     */
    public function add($parent, $menu)
    {
        if ($menu instanceof Menu)
            $menu = ArrayHelper::toArray($menu);
        if (!isset($menu['pos'])) $menu['pos'] = 0;
        if (!isset($menu['id'])) $menu['id'] = end(explode('.', $parent));
        if (is_string($parent)) {
            $parent =  $this->childrenKey($parent . '.' . $menu['id']);
            ArrayHelper::setValue($this->_lists, $parent, $menu);
        } elseif (is_array($parent)) {
            foreach ($parent as $p) {
                $p = $this->childrenKey($p . '.' . $menu['id']);
                ArrayHelper::setValue($this->_lists, $p, $menu);
            }
        }
        return $this;
    }

    /**
     * @param $parent
     * @param Menu[] $menus
     * @return $this
     */
    public function addBatch($parent, $menus)
    {
        foreach ($menus as $menu){
            $this->add($parent, $menu);
        }
        return $this;
    }
    public function normalize(&$menu, $defId)
    {
        if (!isset($menu['id'])) $menu['id'] = $defId;
        if (isset($menu['children']) && !empty($menu['children'])) {
            if(!isset($menu['type'])) $menu['type'] = Menu::TYPE_GROUP;
            foreach ($menu['children'] as $i => $children) {
                $this->normalize($children, $i);
                unset($menu['children'][$i]);
                $menu['children'][$children['id']] = $children;
            }
        }
    }

    private function childrenKey($p)
    {
        return implode('.children.', explode('.', $p));
    }

    /**
     * @param string $parent
     * @param bool $fromBuildMenu jika true maka array yang diambil adalah $this->menu, jika false $this->lists
     * @return mixed
     */
    public function children($parent, $fromBuildMenu = true)
    {
        $data = $fromBuildMenu ? $this->menus : $this->lists;
        return ArrayHelper::getValue($data, $this->childrenKey($parent));
    }


    private function sort_children($children)
    {
        foreach ($children as $childName => $child) {
            if (isset($child['children'])) {
                $child['children'] = $this->sort_children($child['children']);
            }
            $children[$childName] = $child;
        }
        ArrayHelper::multisort($children, 'pos');
        return $children;
    }

    /**
     * Normalize lists
     * @return array
     */
    public function getLists()
    {
        $lists = $this->_lists;
        foreach ($lists as $name => $list) {
            $this->normalize($list, $name);
            $list['children'] = $this->sort_children($list['children']);
            $lists[$name] = $list;

        }
        return $lists;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        $build = [];
        foreach ($this->lists as $name => $list) {
            $build[$name] = new Menu($list);
        }
        return $build;

    }


}