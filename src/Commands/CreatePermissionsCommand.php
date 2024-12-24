<?php

namespace GIS\UserManagement\Commands;

use GIS\UserManagement\Facades\PermissionActions;
use GIS\UserManagement\Models\Permission;
use GIS\UserManagement\Models\Role;
use Illuminate\Console\Command;

class CreatePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'um:permissions {--default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update permissions';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $roles = $this->checkDefaultRoles();
        $permissionsData = config("user-management.permissions");

        $keys = [];
        foreach ($permissionsData as $data) {
            if (empty($data["title"]) || empty($data["policy"]) || empty($data["key"])) {
                $this->error("Not enough data for one of the policy");
                continue;
            }
            $key = $data["key"];
            $keys[] = $key;
            $permission = Permission::query()
                ->where("key", $key)
                ->first();
            if (! $permission) {
                $permission = Permission::create($data);
                $this->info("Create permission {$data['title']} ({$data['key']})");
            } else {
                /**
                 * @var Permission $permission
                 */
                $permission->update($data);
                $this->info("Update permission {$data['title']} ({$data['key']})");
            }

            if ($this->hasOption("default")) {
                $policyClass = $data['policy'];
                foreach ($roles as $role) {
                    PermissionActions::setPermissionByRoleValue($role, $permission, $policyClass::getDefaults());
                    $this->info("Set default rules for {$role->title} by {$permission->title} class");
                }
            }
        }

        $forDelete = Permission::query()
            ->whereNotIn("key", $keys)
            ->get();

        foreach ($forDelete as $item) {
            $item->delete();
        }
    }

    protected function checkDefaultRoles(): array
    {
        $roles = [];
        $defaultRoles = config("user-management.defaultRoles");
        foreach ($defaultRoles as $roleData) {
            if (empty($roleData["name"])) {
                $this->error("Role name not found");
                continue;
            }
            $role = $this->findRoleByName($roleData["name"]);
            if (empty($role)) {
                if (empty($roleData["title"]) || !isset($roleData["management"])) {
                    $this->error("Role data not filled for role {$roleData['name']}");
                    continue;
                }
                $role = Role::create([
                    "name" => $roleData["name"],
                    "title" => $roleData["title"],
                    "management" => $roleData["management"] ? now() : null,
                ]);
            }
            $roles[] = $role;
        }
        return $roles;
    }

    protected function findRoleByName(string $name): ?Role
    {
        return Role::query()->where("name", $name)->first();
    }
}
