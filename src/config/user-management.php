<?php

return [
    'pageUrl' => '/users',
    'customIndexComponent' => null,
    'prefix' => 'admin',
    'as' => "admin.",
    "userPolicy" => \Aweram\UserManagement\Policies\UserPolicy::class,
    "userPolicyTitle" => "Управление пользователями",
    "userPolicyKey" => "users",
    "customUserObserver" => null,

    "rolesUrl" => "/roles",
    "customRoleIndexComponent" => null,
    "rolePolicy" => \Aweram\UserManagement\Policies\RolePolicy::class,
    "rolePolicyTitle" => "Управление ролями",
    "rolePolicyKey" => "roles",

    "permissions" => [],
];
