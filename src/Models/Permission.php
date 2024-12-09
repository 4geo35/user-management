<?php

namespace Aweram\UserManagement\Models;

use Aweram\UserManagement\Observers\PermissionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([PermissionObserver::class])]
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        "policy",
        "title",
        "key",
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot("rights");
    }
}
