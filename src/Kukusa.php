<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yii\base\BaseObject;
use yii\base\UnknownClassException;

defined('KUKUSA_PATH') or define('KUKUSA_PATH', __DIR__);

/**
 * Class Kukusa
 * @mixin Yii
 */
class Kukusa
{
    /**
     * @var array class map used by the Yii autoloading mechanism.
     * The array keys are the class names (without leading backslashes), and the array values
     * are the corresponding class file paths (or [path aliases](guide:concept-aliases)). This property mainly affects
     * how [[autoload()]] works.
     * @see autoload()
     */
    public static $classMap = [];
    /**
     * @var \Kukusa\Web\Application|\Kukusa\Console\Application
     */
    public static $app;
    /**
     * @var array registered path aliases
     * @see getAlias()
     * @see setAlias()
     */
    public static $aliases;
    /**
     * @var yii\di\Container the dependency injection (DI) container used by [[createObject()]].
     * You may use [[Container::set()]] to set up the needed dependencies of classes and
     * their initial property values.
     * @see createObject()
     * @see Container
     */
    public static $container;

    /**
     * @var string
     */
    public static $vendor_path;

    public static function app()
    {
        return self::$app;
    }

    public static function yii()
    {
        return Yii::class;
    }

    public static function bootstrap($vendor_path)
    {
        if (defined('KUKUSA_DEBUG') && defined('KUKUSA_ENV')) {

            defined('YII_DEBUG') or define('YII_DEBUG', KUKUSA_DEBUG);
            defined('YII_ENV') or define('YII_ENV', KUKUSA_ENV);

            defined('KUKUSA_ENV_PROD') or define('KUKUSA_ENV_PROD', KUKUSA_ENV === 'prod');
            defined('KUKUSA_ENV_DEV') or define('KUKUSA_ENV_DEV', KUKUSA_ENV === 'dev');
            /**
             * Whether the the application is running in testing environment.
             */
            defined('KUKUSA_ENV_TEST') or define('KUKUSA_ENV_TEST', KUKUSA_ENV === 'test');
        }
        static::$vendor_path = $vendor_path;
        require rtrim(static::$vendor_path, '/') . '/yiisoft/yii2/yii.php';
        static::setAlias('@kukusa', __DIR__);
        Yii::$classMap['yii\validators\Validator'] = '@kukusa/Validators/Validator.php';
        Yii::$classMap['yii\i18n\MessageSource'] = '@kukusa/i18n/MessageSource.php';
        Kukusa::$container = Yii::$container;
        Kukusa::$classMap = Yii::$classMap;
        Kukusa::$aliases = Yii::$aliases;
        Yii::$container->set('yii\web\JqueryAsset', 'Kukusa\Assets\JqueryAsset');
    }


    /**
     * @param array $config
     * @return \Kukusa\Web\Application
     * @throws \yii\base\InvalidConfigException
     */
    public static function web_app($config)
    {
        $base_config = \Kukusa\Helpers\ArrayHelper::merge(require __DIR__ . '/config/common.php', require __DIR__ . '/config/web.php');
        $config = \Kukusa\Helpers\ArrayHelper::merge($base_config, $config);
        $app = new \Kukusa\Web\Application($config);
        return $app;
    }

    public static function console_app($config)
    {
        $base_config = \Kukusa\Helpers\ArrayHelper::merge(require __DIR__ . '/config/common.php', require __DIR__ . '/config/console.php');
        $config = \Kukusa\Helpers\ArrayHelper::merge($base_config, $config);
        $app = new \Kukusa\Console\Application($config);
        return $app;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([Yii::class, $name], $arguments);
    }

    public static function setObjectValues($object, $values)
    {
        foreach ($values as $key => $value) {
            if($object instanceof BaseObject && $object->canSetProperty($key)){
                if($object->{$key} instanceof BaseObject)
                    self::setObjectValues($object->{$key}, $value);
                else
                    $object->{$key} = $value;
            }
        }
    }
    public static function registerAppAsset($view)
    {
        if(class_exists('\\App\\Assets\\AppAsset')){
            $class = '\\App\\Assets\\AppAsset';
        }else{
            $class = \Kukusa\Assets\AppAsset::class;
        }
        return $class::register($view);

    }
}
