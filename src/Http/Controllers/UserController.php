<?php

namespace GIS\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use GIS\UserManagement\Facades\LoginLinkActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\BufferedOutput;

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

    public function sendLoginLinkForCurrentUserTo(string $email): JsonResponse
    {
        $output = new BufferedOutput;

        Artisan::call("generate:login-link", [
            "email" => Auth::user()->email,
            "--send" => $email,
        ], $output);

        $content = $output->fetch();

        return response()
            ->json($content);
    }

    public function getLoginLinkForCurrentUser(): JsonResponse
    {
        $output = new BufferedOutput;

        Artisan::call("generate:login-link", [
            "email" => Auth::user()->email,
            "--get" => true,
        ], $output);

        $content = $output->fetch();

        return response()
            ->json($content);
    }
}
