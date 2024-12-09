<?php

namespace Aweram\UserManagement\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeSuperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "um:super
                    { --email= : change super status for user by email }
                    { --id= : change super status for user by id }";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change super status for user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($id = $this->option("id")) {
            if (! is_numeric($id)) {
                $this->error("Id need to be numeric");
                return;
            }
            $this->changeById($id);
        } elseif ($email = $this->option("email")) {
            $this->changeByEmail($email);
        } else {
            $this->error("No arguments");
        }
    }

    private function changeById(int $id): void
    {
        $user = User::find($id);
        $this->changeSuper($user);
    }

    private function changeByEmail(string $email): void
    {
        $user = User::query()
            ->where("email", $email)
            ->first();
        $this->changeSuper($user);
    }

    private function changeSuper(User $user = null): void
    {
        if (empty($user)) {
            $this->error("User not found");
            return;
        }
        /**
         * @var User $user
         */
        $super = $user->super;
        $user->super = $super ? null : now();
        $user->save();
        $this->info("User super status has been changed");
    }
}
