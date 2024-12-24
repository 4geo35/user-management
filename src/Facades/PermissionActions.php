<?php

namespace GIS\UserManagement\Facades;

use App\Models\User;
use GIS\UserManagement\Helpers\PermissionActionsManager;
use GIS\UserManagement\Models\Permission;
use GIS\UserManagement\Models\Role;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool checkManagementAccess(User $user)
 * @method static void forgetManagementAccess(User $user)
 * @method static bool allowedAction(User $user, string $permissionKey, int $action)
 *
 * @method static array getRightsByPermissionRoles(array $roleIds, string $key)
 * @method static void forgetRightsByPermissionRole(string $key, int $roleId)
 *
 * @method static null|Permission getPermissionByKey(string $key)
 * @method static void forgetPermissionByKey(string $key)
 *
 * @method static array getRoleIds(User $user)
 * @method static void forgetRoleIds(User $user)
 *
 * @method static array getRightsListByRolePermission(Role $role, Permission $permission)
 * @method static void setPermissionByRoleRights(Role $role, Permission $permission, array $rightsList)
 * @method static void setPermissionByRoleValue(Role $role, Permission $permission, int $rights)
 *
 * @see PermissionActionsManager
 */
class PermissionActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "permission-actions";
    }
}
