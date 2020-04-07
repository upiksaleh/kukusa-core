<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Modules\System\Models;


use Kukusa;
use Kukusa\Base\Model;
use Kukusa\Rbac\RbacHelper;

class AddRoleForm extends Model
{
    public $oldName;
    public $isUpdating = false;
    public $name;
    public $description;
    public function rules()
    {
        return [
            [['name', 'description'], 'required']
        ];
    }

    public function save()
    {
        $am = Kukusa::$app->getAuthManager();
        $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
        if($am->getRole($custom_role_name)){
            $this->addError('name', Kukusa::t('yihai', 'Peran Kustom: "{name}" sudah ada.',['name'=>$this->name]));
            return false;
        }
        try {
            $role = $am->createRole($custom_role_name);
            if ($role) {
                $role->description = $this->description;
                if($am->add($role)) return true;
            }
        }catch (\Exception $e){}
        return false;
    }

    public function update()
    {
        $am = Kukusa::$app->getAuthManager();
        $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
        try {
            $role = $am->getRole($this->oldName);
            if ($role) {
                $role->name = $custom_role_name;
                $role->description = $this->description;
                if($am->update($this->oldName,$role)) return true;
            }
        }catch (\Exception $e){}
        return false;
    }

    public function delete()
    {
        try {
            $am = Kukusa::$app->getAuthManager();
            $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
            if ($role = $am->getRole($custom_role_name)) {
                if($am->remove($role))
                    return true;
            }
        }catch(\Exception $e){}
        return false;
    }
}