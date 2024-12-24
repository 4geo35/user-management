<?php

namespace GIS\UserManagement\Commands;

use App\Models\User;
use GIS\UserManagement\Models\LoginLink;
use Illuminate\Console\Command;

class GenerateLoginLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:login-link {email} {--send=} {--get}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate link for single login to site';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->argument("email");
        $send = $this->option("send");
        if ($this->hasOption("get")) $send = null;
        elseif (empty($send)) $send = $email;

        $user = User::query()
            ->where("email", $email)
            ->first();
        if (! $user) {
            $this->error("User not found");
            return;
        }
        $link = LoginLink::create([
            "email" => $email,
            "send" => $send
        ]);
        $url = route("auth.email-authenticate", ["token" => $link->id]);
        $this->info("Link generated $url $send");
    }
}
