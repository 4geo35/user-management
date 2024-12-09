<?php

namespace GIS\UserManagement\Observers;

use GIS\UserManagement\Facades\PermissionActions;
use GIS\UserManagement\Models\Permission;
use GIS\UserManagement\Models\Role;

class PermissionObserver
{
    public function deleted(Permission $permission): void
    {
        $roles = $permission->roles()->select("id")->get();
        foreach ($roles as $role) {
            /**
             * @var Role $role
             */
            PermissionActions::forgetRightsByPermissionRole($permission->key, $role->id);
        }

        $permission->roles()->sync([]);
        PermissionActions::forgetPermissionByKey($permission->key);
    }

    public function updating(Permission $permission): void
    {
        PermissionActions::forgetPermissionByKey($permission->key);
    }
}
