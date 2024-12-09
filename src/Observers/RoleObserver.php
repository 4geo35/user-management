<?php

namespace GIS\UserManagement\Observers;

use GIS\UserManagement\Facades\PermissionActions;
use GIS\UserManagement\Models\Role;

class RoleObserver
{
    public function updated(Role $role): void
    {
        $users = $role->users()->select("id")->get();
        foreach ($users as $user) {
            PermissionActions::forgetManagementAccess($user);
        }
    }

    public function deleted(Role $role): void
    {
        $users = $role->users()->select("id")->get();
        foreach ($users as $user) {
            PermissionActions::forgetRoleIds($user);
        }
        $role->users()->sync([]);

        $permissions = $role->permissions()->select("id", "key")->get();
        foreach ($permissions as $permission) {
            PermissionActions::forgetRightsByPermissionRole($permission->key, $role->id);
        }
        $role->permissions()->sync([]);
    }
}
