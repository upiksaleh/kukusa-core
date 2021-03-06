<?php

namespace Kukusa\Rbac;


use Exception;
use Kukusa;
use yii\helpers\StringHelper;
use yii\rbac\Item;

class RbacHelper
{

    const TYPE_ROLE = Item::TYPE_ROLE;
    const TYPE_PERMISSION = Item::TYPE_PERMISSION;

    const ROLE_SUPERUSER = 'su';

    public static $idRoleUser = '_user-';
    public static $idRoleRole = '_role-';
    public static $idRoleMenu = '_menu-';
    public static $idRoleModule = '_module-';
    public static $idRoleCustom = '_custom_role-';

    public static function roleSuperuserName()
    {
        return self::roleRoleName(self::ROLE_SUPERUSER);
    }
    public static function userGroupRoleName($group)
    {
        if (is_array($group)) return static::array_or_string(static::$idRoleUser, $group);
        return static::$idRoleUser . $group;
    }

    /**
     * @param string|array $role
     * @return string|array
     */
    public static function roleRoleName($role)
    {
        if (is_array($role)) return static::array_or_string(static::$idRoleRole, $role);
        return static::$idRoleRole . $role;
    }


    public static function roleModuleName($module, $role = '')
    {
        $module = trim($module, '-');
        $role = ($role === '') ? $module : $module . '-' . $role;
        return static::$idRoleModule . $role;
    }

    public static function roleRoleCustomName($role)
    {
        if (is_array($role)) return static::array_or_string(static::$idRoleUser, $role);
        return static::$idRoleCustom . $role;
    }

    public static function menuRoleName($menu)
    {
        $menu = '/' . ltrim($menu, '/');
        return static::$idRoleMenu . $menu;
    }

    private static function array_or_string($prefix, $item)
    {
        $array = [];
        foreach ($item as $i) {
            $array[] = $prefix . $i;
        }
        return $array;

    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsUserGroupName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleUser);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsRoleName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleRole);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsMenuName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleMenu);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsCustomName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleCustom);
    }

    /**
     * @param string $menu
     * @return bool
     */
    public static function checkUserCanMenu($menu)
    {
        if (Kukusa::$app->user->isGuest) return false;
        if (Kukusa::$app->user->can(self::menuRoleName($menu)))
            return true;
        return false;
    }

    /**
     * @param Item $parent
     * @param Item $child
     * @throws \yii\base\Exception
     */
    public static function addChild($parent, $child)
    {
        $auth = Kukusa::$app->getAuthManager();
        if (!$auth->hasChild($parent, $child) && $auth->canAddChild($parent, $child)) {
            $auth->addChild($parent, $child);
        }
    }

    /**
     * @param string $name role/permission name
     * @param int $type Item::TYPE_ROLE|Item::TYPE_PERMISSION
     * @param array|Item $attributes
     * @return \yii\rbac\Permission|\yii\rbac\Role|null
     * @throws Exception
     */
    public static function getAndCreate($name, $type = Item::TYPE_ROLE, $attributes = [])
    {
        $created = FALSE;
        $auth = Kukusa::$app->getAuthManager();
        if ($type === Item::TYPE_ROLE) {
            $role = $auth->getRole($name);
            if (!$role) {
                $role = $auth->createRole($name);
                $created = TRUE;
            }
        } else {
            $role = $auth->getPermission($name);
            if (!$role) {
                $role = $auth->createPermission($name);
                $created = TRUE;
            }
        }
        if ($created) {
            if (is_array($attributes)) {
                foreach ($attributes as $key => $value) {
                    if ($role->canSetProperty($key)) {
                        $role->{$key} = $value;
                    }
                }
            }
            $auth->add($role);
        }
        return $role;
    }

    public static function addRoles($route, $roles = [], $actions = [], $attributes = [])
    {
        if (is_array($route)) {
            foreach ($route as $r) {
                static::addRoleModelRepo($r, $roles, $actions, $attributes);
            }
            return;
        }
        $_actions = [];
        foreach($actions  as $key => $item){
            if(is_int($key))
                $_actions[$item] = [];
            elseif(is_string($key) && is_array($item))
                $_actions[$key] = $item;
        }
        $route = '/' . trim($route, '/');
        $menu_role = static::getAndCreate(static::menuRoleName($route), Item::TYPE_PERMISSION, $attributes);
        if (!empty($roles)) {
            foreach ($roles as $role => $roleAttribute) {
                if(is_string($role) && is_array($roleAttribute))
                    $main_role = static::getAndCreate($role, Item::TYPE_ROLE, $roleAttribute);
                else
                    $main_role = static::getAndCreate($roleAttribute, Item::TYPE_ROLE, []);
                static::addChild($main_role, $menu_role);
            }
        }
        foreach ($_actions as $action => $actionAttribute) {
            $permission_name = static::menuRoleName($route . '/' . trim($action, '/'));
            $permission = static::getAndCreate($permission_name, Item::TYPE_PERMISSION, $actionAttribute);
            static::addChild($menu_role, $permission);
        }
    }
    /**
     * menambah action serta parent
     * @param string|array $route
     * @param array $roles main role
     * @param array $actions
     * @param string $attributes
     * @throws \yii\base\Exception
     */
    public static function addRoleModelRepo($route, $roles = [], $actions = ['index', 'create', 'update', 'delete', 'view', 'import', 'export'], $attributes = [])
    {
        self::addRoles($route,$roles, $actions, array_merge(['description' => 'Model Repo'], $attributes));
    }

    public static function addRoleCrudModule($moduleId, $route, $roles)
    {
        static::addRoleModelRepo($route);
    }

    public static function addRoleCrudRest($route, $roles = [], $actions = [])
    {
        $route = '/' . trim($route, '/') . '/rest';

        self::addRoleModelRepo($route, $roles, $actions);
    }

    /**
     * Hanya menambah role ke action
     * @param string $route
     * @param array $roles
     * @param array $actions
     * @throws \yii\base\Exception
     */
    public static function addRoleToMenuAction($route, $roles, $actions)
    {
        $route = '/' . trim($route, '/');

        foreach ($actions as $action) {
            $permission_name = static::menuRoleName($route . '/' . trim($action, '/'));
            $permission = static::getAndCreate($permission_name, Item::TYPE_PERMISSION);
            foreach ($roles as $role) {
                $parent = static::getAndCreate($role);
                static::addChild($parent, $permission);
            }
        }
    }

    /**
     * @param string $roleName
     * @param int $user_id
     * @throws Exception
     */
    public static function forceAssignRole($roleName, $user_id)
    {
        $auth = Kukusa::$app->getAuthManager();
        $role = static::getAndCreate($roleName, self::TYPE_ROLE);
        try {
            $auth->assign($role, $user_id);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public static function forceRemove($role, $type = 0)
    {
        try {
            $auth = Kukusa::$app->getAuthManager();
            if ($type === self::TYPE_ROLE) {
                $_role = $auth->getRole($role);
            } elseif ($type === self::TYPE_PERMISSION) {
                $_role = $auth->getPermission($role);
            } else {
                if (!$_role = $auth->getPermission($role)) {
                    $_role = $auth->getRole($role);
                }
            }
            $auth->remove($_role);
        } catch (Exception $e) {
        }

    }

    /**
     * @return \yii\rbac\ManagerInterface
     */
    public static function getAuthManager()
    {
        return Kukusa::$app->getAuthManager();
    }

    public static function getRoles()
    {
        $auth = Kukusa::$app->getAuthManager();
        return $auth->getRoles();
    }

    public static function getRolesExcludeUserGroup()
    {
        $roles = static::getRoles();
        foreach ($roles as $name => $role) {
            if (static::roleIsUserGroupName($name))
                unset($roles[$name]);
        }
        return $roles;
    }

    /**
     * @param $userId
     * @return \yii\rbac\Role[]
     */
    public static function getUserRoles($userId)
    {
        $am = Kukusa::$app->getAuthManager();
        return $am->getRolesByUser($userId);
    }


    public static function getRolesRole()
    {
        $r = [];
        foreach (static::getRoles() as $role) {
            if (RbacHelper::roleIsRoleName($role->name)) {
                $r[] = $role;
            }
        }
        return $r;
    }
}