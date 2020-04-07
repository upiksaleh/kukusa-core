<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Console\Controllers;


use Kukusa;
use Kukusa\Base\BaseModelController;
use Kukusa\Console\Controller;
use Kukusa\Rbac\PhpManager;
use Kukusa\Rbac\RbacHelper;
use yii\console\ExitCode;
use yii\helpers\Console;

class SetupController extends Controller
{
    public $defaultAction = 'install';
    /**
     * @var Kukusa\Base\Module[]
     */
    protected $kukusaModules = [];
    /**
     * @var string Module name. Default is "*", all modules will be initialize
     */
    public $moduleName = "*";
    public $devUsername = 'codeup-dev';
    public $devPassword;


    public function init()
    {
        parent::init();
        $this->devPassword = Kukusa::$app->security->generateRandomString(8);
        foreach (Kukusa::$app->getModules() as $name => $config) {
            if (!$this->module->hasModule($name)) continue;
            $m = $this->module->getModule($name);
            if ($m instanceof Kukusa\Base\Module) {
                $this->kukusaModules[$name] = $m;
            }
        }
    }

    public function actionInstall()
    {

//        $this->init_rbac();
//        $migrationController = $this->getMigrationController();
//        $migrationController->interactive = false;
//        $migrationController->migrationPath = ['@yihai-core/migrations'];
//        $migrationController->migrationNamespaces = ['yihai\core\migrations'];
//        $migrationController->runAction('up');
//        $this->moduleMigrateUp();
        $this->actionModule();
        $this->initModelRepo();
//        $migrationController = $this->getMigrationController();
//        $migrationController->interactive = false;
//        $migrationController->migrationPath = ['@yihai/migrations'];
//        $migrationController->migrationNamespaces = ['yihai\migrations'];
//        $migrationController->runAction('up');
//        $this->generateCookieValidationKey(['@yihai/config/web.php']);
//        $this->createUser();

    }

    private function initModelRepo()
    {
        foreach(Kukusa::$app->modelRepo->models as $module => $models){
            foreach($models as $modelKey => $model){
                /** @var BaseModelController $class */
                if(is_string($model))
                    $class = $model;
                elseif(is_array($model) && isset($model['class'])) $class = $model['class'];
                elseif($model instanceof BaseModelController) $class = $model::className();
                foreach($class::modelRepoRoles() as $role => $action){
                    if(is_string($action) && $action === '*')
                        $action = $class::modelRepoActions();
                    RbacHelper::addRoleModelRepo($module.'/'.$modelKey, [$role], $action);
                }
                $class::onSetup();
            }
        }
    }

    public function actionUninstall()
    {
        if ($this->confirm('Sure?')) {
            $migrationPath = ['@yihai/migrations'];
            foreach ($this->kukusaModules as $name => $obj) {
                $migrationPath[] = $obj->getBasePath() . DIRECTORY_SEPARATOR . 'migrations';
            }
            $migrationPath[] = '@yihai-core/migrations';
            $migrationController = $this->getMigrationController();
            $migrationController->interactive = false;
            $migrationController->migrationPath = $migrationPath;
            $migrationController->runAction('down', ['all']);

            $authManager = Kukusa::$app->authManager;
            if ($authManager instanceof PhpManager) {
                foreach ([$authManager->assignmentFile, $authManager->itemFile, $authManager->ruleFile] as $file) {
                    try {
                        unlink($file);
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\n";
                    }
                }
            }
        }
        ExitCode::OK;
    }

    protected function init_rbac()
    {
        $authManager = Kukusa::$app->authManager;
        if ($authManager instanceof PhpManager) {
            foreach ([$authManager->assignmentFile, $authManager->itemFile, $authManager->ruleFile] as $file) {
                $file = Kukusa::getAlias($file);
                if (!is_dir(dirname($file))) {
                    mkdir(dirname($file));
                }
                if (!file_exists($file)) {
                    file_put_contents($file, '<?php return [];');
                    try {
                        chmod($file, octdec("0777"));
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\n";
                    }
                }
            }
        }
    }

    protected function createUser()
    {
        $user = new SysUsersSystem();
        $user->user_username = $this->devUsername;
        $user->user_email = 'info@codeup.id';
        $user->user_password = $this->devPassword;
        $user->fullname = $this->devUsername;
        if ($user->save()) {
            RbacHelper::forceAssignRole(RbacHelper::roleRoleName('superuser'), $user->id);
            $this->stdout("---------USER INFO---------\n", Console::FG_BLUE, Console::BOLD);
            $this->stdout('Username: ', Console::FG_RED, Console::BOLD);
            $this->stdout($this->devUsername, Console::FG_GREEN, Console::BOLD);
            $this->stdout("\n");
            $this->stdout('Password: ', Console::FG_RED, Console::BOLD);
            $this->stdout($this->devPassword, Console::FG_GREEN, Console::BOLD);
            $this->stdout("\n---------------------------\n", Console::FG_BLUE, Console::BOLD);
        }
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['moduleName']);
    }

    public function actionModule()
    {
        if ($this->moduleName === '*') {
            foreach ($this->kukusaModules as $n => $obj) {
                $obj->setup_module();
                $this->stdout('Module: "' . $n . '" success setup.' . "\n");
            }
        } elseif ($obj = $this->getModuleObj($this->moduleName)) {
            $obj->setup_module();
        } else {
            $this->stdout('Module: "' . $this->moduleName . '" Not found.' . "\n");
        }
    }

    public function moduleMigrateUp()
    {
        if ($this->moduleName === '*') {
            foreach ($this->kukusaModules as $n => $obj) {
                $this->stdout("Migrate Up Modules \"{$n}\"\n");
                $migration = $this->getMigrationControllerModule($n);
                $migration->interactive = false;
                $migration->runAction('up');
            }
        } elseif ($this->getModuleObj($this->moduleName)) {
            $this->stdout("Migrate Up Modules \"{$this->moduleName}\"\n");
            $migration = $this->getMigrationControllerModule($this->moduleName);
            $migration->interactive = false;
            $migration->runAction('up');
        } else {
            $this->stdout('Module: "' . $this->moduleName . '" Not found.' . "\n");
        }
    }

    /**
     * @param $name
     * @return bool|\yihai\core\base\Module
     */
    protected function getModuleObj($name)
    {
        if (isset($this->kukusaModules[$name]))
            return $this->kukusaModules[$name];
        return false;
    }

    /**
     * @param $name
     * @return MigrateController
     */
    protected function getMigrationControllerModule($name)
    {
        if (!$module = $this->getModuleObj($name))
            $this->stderr("Not found module.");
        $class = new MigrateController('migrate-' . $name, $this);
        $class->migrationPath = [$module->getBasePath() . DIRECTORY_SEPARATOR . 'migrations'];
        $class->migrationNamespaces = [$module::className()];
        return $class;
    }

    /**
     * @param $name
     * @return MigrateController
     */
    protected function getMigrationController()
    {
        $class = new MigrateController('migrate', $this);
        return $class;
    }

    /**
     * @param array $configs
     * @throws \yii\base\Exception
     */
    public function generateCookieValidationKey($configs)
    {
        $key = Kukusa::$app->security->generateRandomString(32);
        foreach ($configs as $config) {
            $config = Kukusa::getAlias($config);
            if (is_file($config)) {
                $content = file_get_contents($config);
                preg_match('/(("|\')cookieValidationKey("|\')\s*=>\s*)("|\')(.+)("|\')/', $content, $match);
                if (isset($match[5]) && $match[5] === '{cookieValidationKey}') {
                    $content = str_replace('{cookieValidationKey}', $key, $content);
                    file_put_contents($config, $content);
                    $this->stdout("--------------------------\n== cookieValidationKey Generated: {$key} ({$config})\n--------------------------\n");
                }
                $content = preg_replace('/(("|\')cookieValidationKey("|\')\s*=>\s*)(""|\'\')/', "\\1'$key'", $content, -1, $count);
                if ($count > 0) {
                    file_put_contents($config, $content);
                    $this->stdout("--------------------------\n== cookieValidationKey Generated: {$key} ({$config})\n--------------------------\n");
                }
            }
        }
    }

}