<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Theme;


interface ThemeInterface
{

    /**
     * path dari thema
     * ```php
     *  return __DIR__;
     * ```
     * @return string
     */
    public function getPath();

    /**
     * @return array
     */
    public function getContainer();

    /**
     * path map yang akan ditambah pada Yihai::$app->view->theme
     * @return array
     */
    public function getPathMap();

    /**
     * Main Asset Class
     * @return string|\yii\web\AssetBundle
     */
    public function mainAssets();
}