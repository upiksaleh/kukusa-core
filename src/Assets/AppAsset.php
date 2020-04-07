<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Assets;


use Kukusa;
use Kukusa\Web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $logo = 'logo.png';
    public $sourcePath = __DIR__ . '/static/app';

    public function getLogoUrl()
    {
        return $this->baseUrl . '/' . $this->logo;
    }

    public function getDefaultAvatar()
    {
        $url = $this->baseUrl;
        if (!$url)
            $url = Kukusa::$app->assetManager->getBundle(static::class)->baseUrl;

        return $url . '/default_avatar.png';
    }
}