<?php

namespace GIS\UserManagement\Policies;

use App\Models\User;
use GIS\UserManagement\Facades\PermissionActions;
use GIS\UserManagement\Interfaces\PolicyPermissionInterface;
use GIS\UserManagement\Models\Role;

class RolePolicy implements PolicyPermissionInterface
{
    const PERMISSION_KEY = "roles";
    const VIEW_ALL = 2;
    const CREATE = 4;
    const UPDATE = 8;
    const DELETE = 16;

    public static function getPermissions(): array
    {
        return [
            self::VIEW_ALL => __("View all"),
            self::CREATE => __("Creating"),
            self::UPDATE => __("Updating"),
            self::DELETE => __("Deleting")
        ];
    }

    public static function getDefaults(): int
    {
        return 0;
    }

    public function viewAny(User $user): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::VIEW_ALL);
    }

    public function create(User $user): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::CREATE);
    }

    public function update(User $user, Role $role): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::UPDATE);
    }

    public function delete(User $user, Role $role): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::DELETE);
    }
}
