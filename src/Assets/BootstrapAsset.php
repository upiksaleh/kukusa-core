<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Assets;


use Kukusa\Web\AssetBundle;

class BootstrapAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/bootstrap';

    public $css = [
        'css/bootstrap.min.css'
    ];

    public $js = [
        'js/bootstrap.min.js'
    ];
    public $depends = [
        JqueryAsset::class
    ];

}