<?php

namespace GIS\UserManagement\Interfaces;

interface PolicyPermissionInterface
{
    public static function getPermissions(): array;
    public static function getDefaults(): int;
}
