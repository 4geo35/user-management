<?php

namespace Aweram\UserManagement\Helpers;

use App\Models\User;
use Aweram\UserManagement\Models\Permission;
use Aweram\UserManagement\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionActionsManager
{
    const PERMISSION_KEY = "roleRule";
    const ROLE_IDS = "user-role-ids";
    const MANAGEMENT_ID = "user-management-role";

    public function checkManagementAccess(User $user): bool
    {
        return Cache::rememberForever(self::MANAGEMENT_ID . ":{$user->id}", function () use ($user) {
            $role = $user->roles()
                ->whereNotNull("management")
                ->first();
            if ($role) return true;
            else return false;
        });
    }

    public function forgetManagementAccess(User $user): void
    {
        Cache::forget(self::MANAGEMENT_ID . ":{$user->id}");
    }

    public function allowedAction(User $user, string $permissionKey, int $action): bool
    {
        $roleIds = $this->getRoleIds($user);
        $rights = $this->getRightsByPermissionRoles($roleIds, $permissionKey);
        foreach ($rights as $right) {
            if ($right & $action) return true;
        }
        return false;
    }

    public function getRightsByPermissionRoles(array $roleIds, string $key): array
    {
        $rights = [];
        $permission = $this->getPermissionByKey($key);
        foreach ($roleIds as $roleId) {
            $rights[] = Cache::rememberForever("rights:{$roleId}_{$permission->key}", function () use ($roleId, $permission) {
                $row = DB::table("permission_role")
                    ->select("rights")
                    ->where("role_id", $roleId)
                    ->where("permission_id", $permission->id)
                    ->first();
                if (! empty($row)) return $row->rights;
                else return 0;
            });
        }
        return $rights;
    }

    public function forgetRightsByPermissionRole(string $key, int $roleId): void
    {
        Cache::forget("rights:{$roleId}_{$key}");
    }

    public function getPermissionByKey(string $key): ?Permission
    {
        return Cache::rememberForever(self::PERMISSION_KEY . ":{$key}", function () use ($key) {
            return Permission::query()
                ->select("id", "policy", "key")
                ->where("key", $key)
                ->first();
        });
    }

    public function forgetPermissionByKey(string $key): void
    {
        Cache::forget(self::PERMISSION_KEY . ":{$key}");
    }

    public function getRoleIds(User $user): array
    {
        return Cache::rememberForever(self::ROLE_IDS . ":{$user->id}", function () use ($user) {
            $roles = $user->roles()
                ->select("id")
                ->get();

            $ids = [];
            foreach ($roles as $role) {
                $ids[] = $role->id;
            }
            return $ids;
        });
    }

    public function forgetRoleIds(User $user): void
    {
        Cache::forget(self::ROLE_IDS . ":{$user->id}");
        $this->forgetManagementAccess($user);
    }

    public function getRightsListByRolePermission(Role $role, Permission $permission): array
    {
        $currentPermission = $role->permissions()
            ->select("id", "policy")
            ->where("permission_id", $permission->id)
            ->first();

        $array = [];
        if ($currentPermission) {
            $rights = $currentPermission->pivot->rights;
            foreach ($currentPermission->policy::getPermissions() as $key => $value) {
                if ($rights & $key) $array[] = $key;
            }
        }
        return $array;
    }

    public function setPermissionByRoleRights(Role $role, Permission $permission, array $rightsList): void
    {
        $rights = 0;
        foreach ($rightsList as $item) {
            $rights += $item;
        }

        $exist = false;
        $role->load("permissions");
        foreach ($role->permissions as $item) {
            if ($item->key == $permission->key) {
                $exist = true;
                break;
            }
        }

        if ($exist)
            $role->permissions()->updateExistingPivot($permission->id, ["rights" => $rights]);
        else
            $role->permissions()->save($permission, ["rights" => $rights]);

        $this->forgetRightsByPermissionRole($permission->key, $role->id);
    }
}
