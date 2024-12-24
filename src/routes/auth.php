<?php

use Illuminate\Support\Facades\Route;
use GIS\UserManagement\Http\Controllers\UserController;

Route::middleware(["web"])
    ->as("auth.")
    ->group(function () {
        Route::get("auth/email-authenticate/{token}", [UserController::class, "authenticateEmail"])->name("email-authenticate");
    });
