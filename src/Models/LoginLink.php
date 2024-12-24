<?php

namespace GIS\UserManagement\Models;

use App\Models\User;
use GIS\UserManagement\Observers\LoginLinkObserver;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LoginLinkObserver::class])]
class LoginLink extends Model
{
    use Notifiable, HasUuids;

    protected $fillable = [
        "email",
        "send"
    ];

    public function routeNotificationForMail($notification): array
    {
        return [$this->send ? $this->send : $this->email];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "email", "email");
    }
}
