<?php

namespace GIS\UserManagement\Helpers;

use App\Models\User;
use GIS\UserManagement\Models\LoginLink;
use Illuminate\Support\Carbon;

class LoginLinkActionsManager
{
    public function validateFromToken(string $token): ?User
    {
        $link = LoginLink::query()
            ->where("id", $token)
            ->where("created_at", ">", Carbon::parse("-15 minutes"))
            ->first();
        if ($link) {
            $user = $link->user;
            $link->delete();
        } else {
            $user = null;
        }

        $links = LoginLink::query()
            ->where("created_at", "<=", Carbon::parse("-15 minutes"))
            ->get();
        foreach ($links as $link) {
            $link->delete();
        }

        return $user;
    }
}
