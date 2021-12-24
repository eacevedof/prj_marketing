<?php
namespace App\Services\Restrict;
use App\Services\AppService;
use App\Enums\KeyType;

final class DashboardService extends AppService
{
    private array $permissions;
    private array $modules;

    public function __construct()
    {
        $this->permissions = $this->_get_auth()->get_user()[KeyType::AUTH_USER_PERMISSIONS];
        $this->_load_modules();
    }

    private function _load_modules(): void
    {
        $this->modules = [
            "users" => [
                "title" => __("Users"),
                "icon" => "",
                "actions" => [
                    "search" => [
                        "url" => "/restrict/users",
                    ],
                    "create" => [
                        "url" => "/restrict/users/create",
                    ],
                ]
            ],
        ];;
    }

    private function _has_policy(string $policy): bool
    {
        return in_array($policy, $this->permissions);
    }
    
    private function _exclude_nowrite(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            $policy = "$module:write";
            if (!$this->_has_policy($policy))
                unset($tmp[$module]["actions"]["create"]);
        }
        $modules = $tmp;
    }

    private function _exclude_noread(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            $policyr = "$module:read";
            $policyw = "$module:write";
            if (!($this->_has_policy($policyr) || $this->_has_policy($policyw)))
                unset($tmp[$module]["actions"]["search"]);
        }
        $modules = $tmp;
    }

    private function _exclude_empty(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            if (!$tmp[$module]["actions"])
                unset($tmp[$module]);
        }
        $modules = $tmp;
    }
        
    public function __invoke(): array
    {
        $modules = $this->modules;
        $this->_exclude_nowrite($modules);
        $this->_exclude_noread($modules);
        $this->_exclude_empty($modules);
        return $modules;
    }
}