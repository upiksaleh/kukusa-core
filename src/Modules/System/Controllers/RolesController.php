<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Modules\System\Controllers;


use Kukusa\Rbac\RbacHelper;
use Kukusa\Web\Controller;
use Kukusa;
use Kukusa\Widgets\Alert;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use Kukusa\Modules\System\Models\AddRoleForm;
use Kukusa\Modules\System\Models\AssignRoles;

class RolesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'Kukusa\Filters\AccessControl',
                'only' => [
                    'users', 'add-role', 'detail-role', 'roles', 'permissions', 'assign'
                ],
                'rules' => [
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'actions' => [$this->action->id],
                        'roles' => [RbacHelper::menuRoleName($this->action->getUniqueId())],
                    ],
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($this->getUniqueId())],
                    ],
                ],
            ],
        ];
    }

    public function actionRoles()
    {
        return $this->render('list', [
            'type' => Item::TYPE_ROLE,
            'role' => null
        ]);
    }

    public function actionAddRole()
    {
        $model = new AddRoleForm();
        if ($model->load(Kukusa::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Peran "{role}" telah dibuat dengan nama "{custom_role}"', [
                    'role' => $model->name,
                    'custom_role' => RbacHelper::roleRoleCustomName($model->name)
                ]), true);
                return $this->redirect(['roles']);
            } else {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Kukusa::t('kukusa', 'Tidak dapat menambahkan peran.'));
            }
        }
        return $this->render('detail-role', [
            'type' => Item::TYPE_ROLE,
            'model' => $model
        ]);
    }

    public function actionDetailRole($role)
    {
        $authManager = Kukusa::$app->getAuthManager();
        if (!$role = $authManager->getRole($role)) {
            throw new NotFoundHttpException();
        }
        $model = new AddRoleForm();
        $model->name = str_replace(RbacHelper::roleRoleCustomName(''), '', $role->name);
        $model->oldName = $role->name;
        $model->description = $role->description;
        $model->isUpdating = true;

        if ($delete = Kukusa::$app->request->post('_delete')) {
            if ($model->delete()) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Peran "{role}" telah dibuat dengan nama "{custom_role}"', [
                    'role' => $model->name,
                    'custom_role' => RbacHelper::roleRoleCustomName($model->name)
                ]), true);
                return $this->redirect(['roles']);
            } else {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Kukusa::t('kukusa', 'Tidak dapat menghapus peran.'));
            }
        }
        if ($model->load(Kukusa::$app->request->post()) && $model->validate()) {
            if ($model->update()) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Peran "{role}" telah diperbarui.', [
                    'role' => $model->name
                ]), true);
                return $this->redirect(['roles']);
            } else {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Kukusa::t('kukusa', 'Tidak dapat memperbarui peran.'));
            }
        }
        return $this->render('detail-role', [
            'type' => Item::TYPE_ROLE,
            'model' => $model
        ]);
    }


    public function actionPermissions($role = null)
    {
        if ($role && (RbacHelper::roleIsCustomName($role) || RbacHelper::roleIsUserGroupName($role))) {
            if ($postAdd = Kukusa::$app->request->post('add-role')) {
                $am = Kukusa::$app->getAuthManager();
                if (is_array($postAdd) && ($_role = $am->getRole($role))) {
                    foreach ($postAdd as $item) {
                        if ($permission = $am->getRole($item)) {
                            try {
                                $am->addChild($_role, $permission);
                            } catch (\Exception $e) {
                            }
                        }
                    }

                }
            } elseif ($postAdd = Kukusa::$app->request->post('add-permissions')) {
                $am = Kukusa::$app->getAuthManager();
                if (is_array($postAdd) && ($_role = $am->getRole($role))) {
                    foreach ($postAdd as $item) {
                        if ($permission = $am->getPermission($item)) {
                            try {
                                $am->addChild($_role, $permission);
                            } catch (\Exception $e) {
                            }
                        }
                    }

                }
            } else if ($postDel = Kukusa::$app->request->post('delete-child')) {
                $am = Kukusa::$app->getAuthManager();
                try {
                    if (($_role = $am->getRole($role)) && ($permission = $am->getPermission($postDel))) {
                        $am->removeChild($_role, $permission);
                    } elseif (($_role = $am->getRole($role)) && ($permission = $am->getRole($postDel))) {
                        $am->removeChild($_role, $permission);
                    }
                } catch (\Exception $e) {
                }
            }
        }
        return $this->render('list', [
            'type' => Item::TYPE_PERMISSION,
            'role' => $role
        ]);
    }

    public function actionUsers($role = null)
    {

        if (!$role)
            throw new NotFoundHttpException();
        if ($role) {
            if ($postAdd = Kukusa::$app->request->post('assign-user')) {
                $am = Kukusa::$app->getAuthManager();
                if (is_array($postAdd) && ($_role = $am->getRole($role))) {
                    foreach ($postAdd as $item) {
                        try {
                            $am->assign($_role, $item);
                        } catch (\Exception $e) {
                        }
                    }

                }
            } elseif ($postAdd = Kukusa::$app->request->post('delete-assign')) {
                $am = Kukusa::$app->getAuthManager();
                if ($_role = $am->getRole($role)) {
                    try {
                        $am->revoke($_role, $postAdd);
                    } catch (\Exception $e) {
                    }

                }
            }
        }
        return $this->render('list-user', [
            'role' => $role
        ]);
    }

    public function actionAssign()
    {
        // hapus role/permission
        if ($check_id = Kukusa::$app->request->get('check-user_id')) {
            if (($type = Kukusa::$app->request->post('delete-type')) && ($role = Kukusa::$app->request->post('delete-role'))) {
                $authManager = Kukusa::$app->getAuthManager();
                if ($type == Item::TYPE_ROLE && ($roleItem = $authManager->getRole($role)) && ($assign = $authManager->getAssignment($role, $check_id))) {
                    try {
                        $authManager->revoke($roleItem, $check_id);
                        Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Berhasil menghapus peran pengguna.'), true);
                        return $this->redirect(['assign', 'check-user_id' => $check_id]);
                    } catch (\Exception $e) {

                    }
                } elseif ($type == Item::TYPE_PERMISSION && ($roleItem = $authManager->getPermission($role)) && ($assign = $authManager->getAssignment($role, $check_id))) {
                    try {
                        $authManager->revoke($roleItem, $check_id);
                        Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Berhasil menghapus izin peran pengguna.'), true);
                        return $this->redirect(['assign', 'check-user_id' => $check_id]);
                    } catch (\Exception $e) {

                    }
                }

            }
        }
        $model = new AssignRoles();
        if ($model->load(Kukusa::$app->request->post()) && $model->validate()) {
            $authManager = Kukusa::$app->getAuthManager();
            $error = [];
            if ($model->role) {
                $role = $authManager->getRole($model->role);
                if ($role) {
                    echo $model->user_id;
                    if ($authManager->getAssignment($role->name, $model->user_id)) {
                        $error[] = Kukusa::t('kukusa', '"{role}" telah ditetapkan untuk pengguna "{user_id}"', [
                            'role' => $role->name,
                            'user_id' => $model->user_id
                        ]);
                    } else {
                        $authManager->assign($role, $model->user_id);
                        Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Sukses menetapkan peran.'), true);
                    }
                }
            }
            if ($model->permission) {
                $role = $authManager->getPermission($model->permission);
                if ($role) {
                    if ($authManager->getAssignment($role->name, $model->user_id)) {
                        $error[] = Kukusa::t('kukusa', '"{role}" telah ditetapkan untuk pengguna "{user_id}"', [
                            'role' => $role->name,
                            'user_id' => $model->user_id
                        ]);
                    } else {
                        $authManager->assign($role, $model->user_id);
                        Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('kukusa', 'Sukses menetapkan peran.'), true);
                    }
                }
            }
            if ($error)
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', implode('<br/>', $error));
            return $this->redirect(['assign']);
        }
        return $this->render('assign', [
            'model' => $model
        ]);
    }
}