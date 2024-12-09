<?php

namespace Aweram\UserManagement\Policies;

use App\Models\User;
use Aweram\UserManagement\Facades\PermissionActions;
use Aweram\UserManagement\Interfaces\PolicyPermissionInterface;

class UserPolicy implements PolicyPermissionInterface
{
    const PERMISSION_KEY = "users";
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

    public function viewAny(User $user): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::VIEW_ALL);
    }

    public function create(User $user): bool
    {
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::CREATE);
    }

    public function update(User $user, User $model): bool
    {
        if ($model->super && $user->id != $model->id) return false;
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::UPDATE);
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->super) return false;
        return PermissionActions::allowedAction($user, self::PERMISSION_KEY, self::DELETE);
    }
}
