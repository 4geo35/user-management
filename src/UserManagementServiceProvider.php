<?php

namespace GIS\UserManagement;

use App\Models\User;
use GIS\UserManagement\Commands\ChangeSuperCommand;
use GIS\UserManagement\Commands\CreatePermissionsCommand;
use GIS\UserManagement\Facades\PermissionActions;
use GIS\UserManagement\Helpers\PermissionActionsManager;
use GIS\UserManagement\Http\Middleware\AppManagement;
use GIS\UserManagement\Http\Middleware\SuperUser;
use GIS\UserManagement\Livewire\RoleIndexWire;
use GIS\UserManagement\Livewire\UserIndexWire;
use GIS\UserManagement\Models\Role;
use GIS\UserManagement\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class UserManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Подключение views
        $this->loadViewsFrom(__DIR__ . "/resources/views", "um");

        // Livewire
        // Users
        $component = config("user-management.customIndexComponent");
        Livewire::component(
            "um-users",
            $component ?? UserIndexWire::class
        );
        // Roles
        $component = config("user-management.customRoleIndexComponent");
        Livewire::component(
            "um-roles",
            $component ?? RoleIndexWire::class
        );

        // Middleware
        $this->app["router"]->aliasMiddleware("app-management", AppManagement::class);
        $this->app["router"]->aliasMiddleware("super-user", SuperUser::class);

        // Policy
        Gate::before(function (User $user, string $ability) {
            if ($user->super) return true;
        });
        Gate::policy(User::class, config("user-management.userPolicy"));
        Gate::policy(Role::class, config("user-management.rolePolicy"));
        Gate::define("app-management", function (User $user) {
            return PermissionActions::checkManagementAccess($user);
        });
        Gate::define("super-user", function (User $user) {
            return ! empty($user->super);
        });

        // Наблюдатели
        $userObserverClass = config("user-management.customUserObserver") ?? UserObserver::class;
        User::observe($userObserverClass);

        // Добавить политики в конфигурацию
        $this->expandConfiguration();
    }

    public function register(): void
    {
        // Миграции.
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");

        // Подключение конфигурации
        $this->mergeConfigFrom(
            __DIR__ . "/config/user-management.php", "user-management"
        );

        // Подключение routes
        $this->loadRoutesFrom(__DIR__ . "/routes/admin.php");

        // Подключение переводов
        $this->loadJsonTranslationsFrom(__DIR__ . "/lang");

        // Facades
        $this->app->singleton("permission-actions", function () {
            return new PermissionActionsManager;
        });

        // Commands.
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatePermissionsCommand::class,
                ChangeSuperCommand::class,
            ]);
        }
    }

    private function expandConfiguration(): void
    {
        $um = app()->config["user-management"];
        $permissions = $um["permissions"];
        $permissions[] = [
            "title" => $um["userPolicyTitle"],
            "policy" => $um["userPolicy"],
            "key" => $um["userPolicyKey"]
        ];
        $permissions[] = [
            "title" => $um["rolePolicyTitle"],
            "policy" => $um["rolePolicy"],
            "key" => $um["rolePolicyKey"]
        ];
        app()->config["user-management.permissions"] = $permissions;
    }
}
