<?php

namespace GIS\UserManagement\Traits;

use GIS\UserManagement\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ShouldRole
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
