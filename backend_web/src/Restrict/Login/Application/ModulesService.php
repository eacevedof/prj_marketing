<?php
namespace App\Restrict\Login\Application;

use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Enums\SessionType;

final class ModulesService
{
    private AuthService $auth;
    private array $permissions;
    private array $modules;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->permissions = $this->auth->get_user()[SessionType::AUTH_USER_PERMISSIONS] ?? [];
        if ($this->auth->is_root_super())
            $this->permissions = UserPolicyType::get_all();
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
            "subscriptions" => [
                "title" => __("Subscriptions"),
                "icon" => "la-gift",
                "actions" => [
                    "search" => [
                        "url" => "/restrict/subscriptions",
                    ],
                    "edit" => [
                        //"url" => "/restrict/subscriptions/update",
                    ],
                ]
            ],
            "billings" => [
                "title" => __("Billings"),
                "icon" => "la-gift",
                "actions" => [
                    "search" => [
                        "url" => "/restrict/billings",
                    ],
                ]
            ],
        ];

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
            if (!$this->_has_policy($policy)) {
                unset($tmp[$module]["actions"]["create"]);
                unset($tmp[$module]["actions"]["edit"]);
            }
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
        if ($this->auth->is_root() && !$this->permissions)
            return $modules;
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