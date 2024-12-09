<?php

return [
    'pageUrl' => '/users',
    'customIndexComponent' => null,
    'prefix' => 'admin',
    'as' => "admin.",
    "userPolicy" => \GIS\UserManagement\Policies\UserPolicy::class,
    "userPolicyTitle" => "Управление пользователями",
    "userPolicyKey" => "users",
    "customUserObserver" => null,

    "rolesUrl" => "/roles",
    "customRoleIndexComponent" => null,
    "rolePolicy" => \GIS\UserManagement\Policies\RolePolicy::class,
    "rolePolicyTitle" => "Управление ролями",
    "rolePolicyKey" => "roles",

    "permissions" => [],
];
