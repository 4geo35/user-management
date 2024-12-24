<?php

namespace GIS\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use GIS\UserManagement\Facades\LoginLinkActions;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function authenticateEmail(string $token)
    {
        $user = LoginLinkActions::validateFromToken($token);
        if ($user) {
            Auth::login($user);
            return redirect()->route("login");
        } else {
            return redirect()->route("login")->with("error", "Некорректный токен");
        }
    }
}
