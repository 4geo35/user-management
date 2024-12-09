@php
    $routeName = config('user-management.as') . 'users';
    $roleRouteName = config("user-management.as") . "roles";
@endphp

@can('viewAny', \App\Models\User::class)
    <x-tt::admin-menu.item href="{{ route($routeName) }}" :active="\Illuminate\Support\Facades\Route::currentRouteName() == $routeName">
        <x-slot name="ico"><x-um::ico.users /></x-slot>
        {{ __("Users") }}
    </x-tt::admin-menu.item>
@endcan

@can("viewAny", \Aweram\UserManagement\Models\Role::class)
    <x-tt::admin-menu.item href="{{ route($roleRouteName) }}" :active="\Illuminate\Support\Facades\Route::currentRouteName() == $roleRouteName">
        <x-slot name="ico"><x-um::ico.roles /></x-slot>
        {{ __("Roles") }}
    </x-tt::admin-menu.item>
@endcan
