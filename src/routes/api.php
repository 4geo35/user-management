<?php

use Illuminate\Support\Facades\Route;
use GIS\UserManagement\Http\Controllers\UserController;

Route::middleware(["auth:api", "super-user"])
    ->prefix("api")
    ->as("api.users.")
    ->group(function () {
        Route::get("user", function () {
            $user = \Illuminate\Support\Facades\Auth::user();
            return response()
                ->json([
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ]);
        })->name("user");

        Route::get("/auth/{email}/send-link", [UserController::class, "sendLoginLinkForCurrentUserTo"])
            ->name("get-current-link");

        Route::get("/auth/get-link", [UserController::class, "getLoginLinkForCurrentUser"])
            ->name("get-login-link");
    });
