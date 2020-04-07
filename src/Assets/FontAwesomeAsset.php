<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Assets;


use Kukusa\Web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/font-awesome';
    public $css = [
        'css/all.min.css'
    ];
}