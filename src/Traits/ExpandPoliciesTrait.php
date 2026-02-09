<?php

namespace GIS\UserManagement\Traits;

trait ExpandPoliciesTrait
{
    protected function expandPolicies(array $config, array $policies): void
    {
        $um = app()->config["user-management"];
        $permissions = $um["permissions"];

        foreach ($policies as $policy) {
            $array = $this->getPolicyArrayFromConfig($config, $policy);
            if (empty($array)) { continue; }
            $permissions[] = $array;
        }

        app()->config["user-management.permissions"] = $permissions;
    }

    protected function getPolicyArrayFromConfig(array $config, string $policy): ?array
    {
        if (empty($config[$policy])) { return null; }
        $policyKey = $policy . "Key";
        if (empty($config[$policyKey])) { return null; }
        $policyTitle = $policy . "Title";
        if (empty($config[$policyTitle])) { return null; }

        return [
            "title" => $config[$policyTitle],
            "key" => $config[$policyKey],
            "policy" => $config[$policy],
        ];
    }
}
