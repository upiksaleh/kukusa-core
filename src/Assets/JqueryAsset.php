<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Assets;


use Kukusa\Web\AssetBundle;

class JqueryAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/jquery';
    public $js = [
        'jquery.min.js'
    ];
}