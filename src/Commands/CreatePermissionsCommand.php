<?php

namespace Aweram\UserManagement\Commands;

use Aweram\UserManagement\Models\Permission;
use Illuminate\Console\Command;

class CreatePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'um:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissionsData = config("user-management.permissions");

        $keys = [];
        foreach ($permissionsData as $data) {
            $key = $data["key"];
            $keys[] = $key;
            $permission = Permission::query()
                ->where("key", $key)
                ->first();
            if (! $permission) {
                Permission::create($data);
                $this->info("Create permission {$data['title']} ({$data['key']})");
            } else {
                /**
                 * @var Permission $permission
                 */
                $permission->update($data);
                $this->info("Update permission {$data['title']} ({$data['key']})");
            }
        }

        $forDelete = Permission::query()
            ->whereNotIn("key", $keys)
            ->get();

        foreach ($forDelete as $item) {
            $item->delete();
        }
    }
}
