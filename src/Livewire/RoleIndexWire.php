<?php

namespace Aweram\UserManagement\Livewire;

use Aweram\UserManagement\Facades\PermissionActions;
use Aweram\UserManagement\Models\Permission;
use Aweram\UserManagement\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class RoleIndexWire extends Component
{
    use WithPagination;

    public bool $displayData = false;
    public bool $displayDelete = false;
    public int|null $roleId = null;

    public string $name = "";
    public string $title = "";
    public bool $management = false;
    public int|null $permissionId = null;

    public bool $displayPermissions = false;
    public string $permissionTitle = "";
    public array $permissionList = [];
    public array $rolePermissions = [];

    public function rules(): array
    {
        $uniqueCondition = "unique:roles,name";
        if ($this->roleId) $uniqueCondition .= ",{$this->roleId}";

        $uniqueTitleCondition = "unique:roles,name";
        if ($this->roleId) $uniqueTitleCondition .= ",{$this->roleId}";

        return [
            "name" => ["required", "string", "max:20", $uniqueCondition],
            "title" => ["required", "string", "max:50", $uniqueTitleCondition]
        ];
    }

    public function validationAttributes(): array
    {
        return [
            "name" => __("Name"),
            "title" => __("Title"),
        ];
    }

    public function render()
    {
        $query = Role::query()->select("id", "name", "title");
        $query->orderBy("title");
        $roles = $query->paginate();

        $query = Permission::query()->select("id", "key", "title");
        $query->orderBy("title");
        $permissions = $query->get();

        return view('um::livewire.admin.roles', [
            "roles" => $roles,
            "permissions" => $permissions,
        ]);
    }

    public function showCreate(): void
    {
        $this->resetFields();
        // Проверить авторизацию
        $check = $this->checkAuth("create");
        if (! $check) return;

        $this->displayData = true;
    }

    public function store(): void
    {
        // Проверить авторизацию
        $check = $this->checkAuth("create");
        if (! $check) return;
        // Валидация
        $this->validate();

        Role::create([
            "name" => $this->name,
            "title" => $this->title,
            "management" => $this->management ? now() : null,
        ]);

        session()->flash("success", __("Role successfully added"));
        $this->closeData();
        $this->resetPage();
    }

    public function showEdit(int $roleId): void
    {
        $this->resetFields();
        $this->roleId = $roleId;
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $role);
        if (! $check) return;

        $this->name = $role->name;
        $this->title = $role->title;
        $this->management = ! empty($role->management);
        $this->displayData = true;
    }

    public function update(): void
    {
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $role);
        if (! $check) return;
        // Валидация
        $this->validate();
        try {
            $role->update([
                "name" => $this->name,
                "title" => $this->title,
                "management" => $this->management ? now() : null,
            ]);
            session()->flash("success", __("Role successfully updated"));
        } catch (\Exception $ex) {
            session()->flash("error", __("Error while update"));
        }

        $this->resetPage();
        $this->closeData();
    }

    public function closeData(): void
    {
        $this->resetFields();
        $this->displayData = false;
    }

    public function showDelete(int $roleId): void
    {
        $this->resetFields();
        $this->roleId = $roleId;
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Проверить авторизацию
        $check = $this->checkAuth("delete", $role);
        if (! $check) return;

        $this->displayDelete = true;
    }

    public function closeDelete(): void
    {
        $this->displayDelete = false;
        $this->resetFields();
    }

    public function confirmDelete(): void
    {
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Проверить авторизацию
        $check = $this->checkAuth("delete", $role);
        if (! $check) return;

        try {
            $role->delete();
            session()->flash("success", __("Role successfully deleted"));
        } catch (\Exception $ex) {
            session()->flash("error", __("Role not found"));
        }

        $this->resetPage();
        $this->closeDelete();
    }

    public function showPermissions(int $permissionId, int $roleId): void
    {
        $this->resetFields();
        $this->roleId = $roleId;
        $this->permissionId = $permissionId;
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Найти права доступа
        $permission = $this->findPermission();
        if (! $permission) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $role);
        if (! $check) return;

        $this->displayPermissions = true;
        $this->permissionTitle = $permission->title;
        $this->permissionList = $permission->policy::getPermissions();
        $this->rolePermissions = PermissionActions::getRightsListByRolePermission($role, $permission);
    }

    public function updatePermissions(): void
    {
        // Найти роль
        $role = $this->findRole();
        if (! $role) return;
        // Найти права доступа
        $permission = $this->findPermission();
        if (! $permission) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $role);
        if (! $check) return;

        PermissionActions::setPermissionByRoleRights($role, $permission, $this->rolePermissions);
        session()->flash("success", __("Permissions successfully updated"));

        $this->closePermissions();
    }

    public function closePermissions(): void
    {
        $this->resetFields();
        $this->displayPermissions = false;
    }

    private function resetFields(): void
    {
        $this->reset(["name", "title", "roleId", "rolePermissions", "permissionId", "permissionTitle", "management"]);
    }

    private function checkAuth(string $action, Role $role = null): bool
    {
        try {
            $this->authorize($action, $role ?? Role::class);
            return true;
        } catch (\Exception $ex) {
            session()->flash("error", __("Unauthorized action"));
            $this->closeData();
            $this->closePermissions();
            $this->closeDelete();
            return false;
        }
    }

    private function findRole(): ?Role
    {
        $role = Role::find($this->roleId);
        if (! $role) {
            session()->flash("error", __("Role not found"));
            $this->closeData();
            $this->closeDelete();
            return null;
        }
        return $role;
    }

    private function findPermission(): ?Permission
    {
        $permission = Permission::find($this->permissionId);
        if (! $permission) {
            session()->flash("error", __("Permission not found"));
            $this->closePermissions();
            return null;
        }
        return $permission;
    }
}
