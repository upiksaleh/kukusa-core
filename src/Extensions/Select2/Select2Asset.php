<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Extensions\Select2;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';
    public $js = [
        'js/select2.full.min.js'
    ];
    public $css = [
        'css/select2.min.css',
        'css/select2theme.css',
    ];
    public $depends = [
        'Kukusa\Assets\JqueryAsset'
    ];
}