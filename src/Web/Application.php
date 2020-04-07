<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa;

/**
 * Class Application
 * @package Kukusa\Web
 * @property User $user
 * @property \Kukusa\Base\ModelRepo $modelRepo
 * @property Kukusa\JWT\Jwt $jwt
 * @property Kukusa\Base\Config $config
 * @property Kukusa\i18n\I18N $i18n
 * @property Kukusa\Theme\ThemeComponent $theme
 * @property Kukusa\Web\WebMenu $webMenu
 *
 */
class Application extends \yii\web\Application
{
    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        Kukusa::$app = $this;
        parent::__construct($config);
    }
}