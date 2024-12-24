<?php

namespace GIS\UserManagement\Observers;

use GIS\UserManagement\Models\LoginLink;
use GIS\UserManagement\Notifications\GeneratedLoginLink;
use Illuminate\Support\Str;

class LoginLinkObserver
{
    public function created(LoginLink $link): void
    {
        if (! empty($link->send)) {
            $link->notify(new GeneratedLoginLink());
        }
    }
}
