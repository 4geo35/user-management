<?php

namespace GIS\UserManagement\Observers;

use App\Models\User;
use GIS\UserManagement\Facades\PermissionActions;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->ip_address = request()->ip();
    }
    public function deleted(User $user): void
    {
        $user->roles()->sync([]);
        PermissionActions::forgetRoleIds($user);
    }
}
