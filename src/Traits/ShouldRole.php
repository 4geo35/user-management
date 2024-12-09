<?php

namespace Aweram\UserManagement\Traits;

use Aweram\UserManagement\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ShouldRole
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
