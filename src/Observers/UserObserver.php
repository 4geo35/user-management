<?php

namespace GIS\UserManagement\Observers;

use App\Models\User;
use GIS\UserManagement\Facades\PermissionActions;

class UserObserver
{
    public function deleted(User $user): void
    {
        $user->roles()->sync([]);
        PermissionActions::forgetRoleIds($user);
    }
}
