<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Assets;


use Kukusa\Web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $publishOptions = [
        'forceCopy' => KUKUSA_ENV_DEV
    ];
    public $sourcePath = __DIR__ .'/static/main';

    public $css = [
        'css/main.min.css',
        'css/skins/_all-skins.min.css',
        'css/styles.css'
    ];

    public $js = [
        KUKUSA_ENV_DEV ? 'js/scripts.js' : 'js/min.js'
    ];

    public $depends = [
        FontAwesomeAsset::class,
        BootstrapAsset::class,
        ICheckAsset::class,
//        'yihai\core\assets\BootstrapAsset',
//        'yihai\core\assets\JquerySlimScrollAsset',
//        'yii\web\YiiAsset',
    ];
}