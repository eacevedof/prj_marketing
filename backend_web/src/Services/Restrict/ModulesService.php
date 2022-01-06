<?php
namespace App\Services\Restrict;
use App\Factories\ServiceFactory as SF;
use App\Enums\SessionType;

final class ModulesService
{
    private array $permissions;
    private array $modules;

    public function __construct()
    {
        $this->permissions = SF::get_auth()->get_user()[SessionType::AUTH_USER_PERMISSIONS] ?? [];
        $this->_load_modules();
    }

    private function _load_modules(): void
    {
        $this->modules = [
            "users" => [
                "title" => __("Users"),
                "icon" => "la-user-circle",
                "actions" => [
                    "search" => [
                        "url" => "/restrict/users",
                    ],
                    "create" => [
                        "url" => "/restrict/users/create",
                    ],
                ]
            ],
            "promotions" => [
                "title" => __("Promotions"),
                "icon" => "la-gift",
                "actions" => [
                    "search" => [
                        "url" => "/restrict/promotions",
                    ],
                    "create" => [
                        "url" => "/restrict/promotions/create",
                    ],
                ]
            ],
        ];;
    }// _load_modules

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

    private function _get_filtered_modules(): array
    {
        $modules = $this->modules;
        $this->_exclude_nowrite($modules);
        $this->_exclude_noread($modules);
        $this->_exclude_empty($modules);
        return $modules;
    }
        
    public function __invoke(): array
    {
        return $this->_get_filtered_modules();
    }

    public function get_menu(): array
    {
        $modules = $this->_get_filtered_modules();
        $tmp = [];
        foreach ($modules as $module => $config) {
            $tmp[$module] = [
                "title" => $config["title"],
                "search" => $config["actions"]["search"]["url"] ?? ""
            ];
        }
        return $tmp;
    }
}