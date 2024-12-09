<?php

namespace Aweram\UserManagement\Observers;

use App\Models\User;
use Aweram\UserManagement\Facades\PermissionActions;

class UserObserver
{
    public function deleted(User $user): void
    {
        $user->roles()->sync([]);
        PermissionActions::forgetRoleIds($user);
    }
}
