<?php

namespace GIS\UserManagement\Facades;

use App\Models\User;
use GIS\UserManagement\Helpers\LoginLinkActionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static User|null validateFromToken(string $token)
 *
 * @see LoginLinkActionsManager
 */
class LoginLinkActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "login-link-actions";
    }
}
