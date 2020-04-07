<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa;


use Kukusa;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class Bootstrap implements BootstrapInterface
{

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \Kukusa\Web\Application) {
            // bootstrap theme component
            $app->theme->bootstrap();
        }

        foreach ($app->modules as $name => $module) {
            try {
                $module = $app->getModule($name);
            } catch (InvalidConfigException $e) {
                if (is_dir(Kukusa::getAlias('@app/Modules/' . $name . '/src'))) {
                    Kukusa::setAlias('@app/Modules/' . $name, Kukusa::getAlias('@app/Modules/' . $name . '/src'));
                } elseif (is_dir(Kukusa::getAlias('@app/Modules/module-' . $name . '/src'))) {
                    Kukusa::setAlias('@app/Modules/' . $name, Kukusa::getAlias('@app/Modules/module-' . $name . '/src'));
                } elseif (is_dir(Kukusa::getAlias('@app/Modules/kukusa-module-' . $name . '/src'))) {
                    Kukusa::setAlias('@app/Modules/' . $name, Kukusa::getAlias('@app/Modules/kukusa-module-' . $name . '/src'));
                }
                $module = $app->getModule($name);
            }
            if ($module instanceof Kukusa\Base\Module) {
                $module->boot_all($app);
                if ($app instanceof Kukusa\Web\Application) {
                    $module->boot_web($app);
                } elseif ($app instanceof Kukusa\Console\Application)
                    $module->boot_console($app);

                // check required module
                foreach ($module->requiredModule as $requireModule) {
                    if (!$app->hasModule($requireModule))
                        throw new InvalidConfigException("Module '{$requireModule}' dibutuhkan untuk modul '{$name}'");
                }
            }
        }

    }
}