<?php

use Illuminate\Support\Facades\Route;

Route::middleware(["auth:api", "super-user"])
    ->prefix("api")
    ->as("api.")
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
    });
