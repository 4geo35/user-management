<?php

use Illuminate\Support\Facades\Route;

Route::middleware(["web", 'auth', "app-management"])
    ->prefix(config("user-management.prefix"))
    ->as(config("user-management.as"))
    ->group(function () {
        Route::get(config("user-management.pageUrl"), function () {
            return view("um::admin.users");
        })->name("users")->middleware("can:viewAny,App\Models\User");

        Route::get(config("user-management.rolesUrl"), function () {
            return view("um::admin.roles");
        })->name("roles")->middleware("can:viewAny,Aweram\UserManagement\Models\Role");
    });
