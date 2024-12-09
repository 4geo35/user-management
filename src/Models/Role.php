<?php

namespace Aweram\UserManagement\Models;

use App\Models\User;
use Aweram\UserManagement\Observers\RoleObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([RoleObserver::class])]
class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "title",
        "management",
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot("rights");
    }
}
