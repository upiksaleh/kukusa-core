<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Modules\System;


use Kukusa;
use Kukusa\Base\ModelOptions;
use Kukusa\Controllers\ModelRepoController;
use Kukusa\Rbac\RbacHelper;
use Kukusa\Web\Menu;
use yii\base\InvalidArgumentException;
use Kukusa\Helpers\ArrayHelper;

class Module extends \Kukusa\Base\Module
{
    public $layout = 'backend';

    public $requiredModule = [];
    public $controllerMap = [

    ];

    public function __construct($id, $parent = null, $config = [])
    {
        if (!isset($config['layoutPath']))
            $config['layoutPath'] = '@kukusa/views/_layouts';
        parent::__construct($id, $parent, $config);
    }

    public function boot_all($app)
    {
        parent::boot_all($app);
        $app->modelRepo->addBatchModule($this->id, [
            'sys-groups' => 'Kukusa\Models\SysGroups',
            'sys-users' => 'Kukusa\Models\SysUser',
            'sys-users-system' => 'Kukusa\Models\SysUsersSystem',
            'rbac-roles' => 'Kukusa\Models\rbac\RolesModel',
            'rbac-roles2' => 'Kukusa\Models\rbac\RolesModel2',
            'rbac-permissions' => 'Kukusa\Models\rbac\Permissions',
        ]);
        $app->modelRepo->add($this->id, 'system-data', [
            'class' => \Kukusa\Models\DataModel::class,
        ]);
        $app->modelRepo->add($this->id, 'system-users', [
            'class' => \Kukusa\Models\SysUsersSystem::class,
        ]);
    }

    public function boot_web($app)
    {
        parent::boot_web($app);
        $app->getUrlManager()->addRules([
            'system/roles/<action>' => '/system/roles/<action>',
            'system/<action:(login|index)>' => '/system/default/<action>',
        ], false);
    }

    /**
     * @inheritDoc
     */
    protected function web_menu_init($menu)
    {
        $menu->add('backend', ['id' => $this->id, 'type' => Menu::TYPE_HEADER])
            ->addBatch(['backend.system', 'modules.' . $this->id], [
                ['id' => 'users', 'label' => Kukusa::t('kukusa', 'Pengguna'), 'type' => Menu::TYPE_GROUP, 'children' => [
                    ['id' => 'groups', 'route' => $this->id . '/sys-groups', 'isModelRepo' => true],
                    ['id' => 'sys-users', 'route' => $this->id . '/sys-users', 'isModelRepo' => true],
                    ['id' => 'sys-users-system', 'route' => $this->id . '/sys-users-system', 'isModelRepo' => true],
                ]],
                ['id' => 'settings', 'label' => Kukusa::t('kukusa', 'Pengaturan'), 'type' => Menu::TYPE_GROUP, 'children' => [
                    ['id' => 'rbac', 'label' => 'Roles Control', 'type' => Menu::TYPE_GROUP, 'children' => [
                        ['id' => 'roles', 'label' => 'Roles', 'route' => '/' . $this->id . '/roles/roles'],
                        ['id' => 'permissions', 'label' => 'Permissions', 'route' => '/' . $this->id . '/roles/permissions'],
                        ['id' => 'assign', 'label' => 'Assign', 'route' => '/' . $this->id . '/roles/assign'],
//                        ['id' => 'permissions', 'label' => 'Permissions', 'route' => $this->id . '/rbac-permissions', 'isModelRepo' => true]
                    ]]
                ]],
//                ['id' => 'settings', 'label' => Kukusa::t('kukusa', 'Pengaturan'), 'type' => Menu::TYPE_GROUP, 'children' => [
//                    ['id' => 'rbac', 'label' => 'Roles Control', 'type' => Menu::TYPE_GROUP, 'children' => [
//                        ['id' => 'roles', 'label' => 'Roles', 'route' => $this->id . '/rbac-roles', 'isModelRepo' => true],
//                        ['id' => 'permissions', 'label' => 'Permissions', 'route' => $this->id . '/rbac-permissions', 'isModelRepo' => true]
//                    ]]
//                ]]
            ]);
    }

    protected function onSetup()
    {
        RbacHelper::addRoles($this->id . '/roles/', [RbacHelper::roleSuperuserName()], ['roles', 'permissions', 'assign', 'add-role', 'detail-role', 'users'], ['description' => 'Roles Control']);

//        RbacHelper::addRoleModelRepo('a');
//        RbacHelper::getAndCreate(RbacHelper::roleRoleName('superuser'), RbacHelper::TYPE_ROLE, ['description' => 'Have access to all menu in system.']);
    }
}