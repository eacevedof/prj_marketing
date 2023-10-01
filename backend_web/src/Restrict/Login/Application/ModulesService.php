<?php

namespace App\Restrict\Login\Application;

use App\Shared\Domain\Enums\SessionType;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;

final class ModulesService
{
    private AuthService $authService;
    private array $allowedPolicies;
    private array $modules;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->allowedPolicies = $this->authService->getAuthUserArray()[SessionType::AUTH_USER_PERMISSIONS] ?? [];
        if ($this->authService->isAuthUserSuperRoot()) {
            $this->allowedPolicies = UserPolicyType::getAllPolicies();
        }
        $this->_loadModules();
    }

    private function _loadModules(): void
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
                        "url" => Routes::getUrlByRouteName("qr.validate"),
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

    private function _isAllowedPolicy(string $policy): bool
    {
        return in_array($policy, $this->allowedPolicies);
    }

    private function _removeNoWriteableActions(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            $policy = "$module:write";
            if (!$this->_isAllowedPolicy($policy)) {
                unset($tmp[$module]["actions"]["create"]);
                unset($tmp[$module]["actions"]["edit"]);
            }
        }
        $modules = $tmp;
    }

    private function _removeNoReadableActions(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            $policyRead = "$module:read";
            $policyWrite = "$module:write";
            if (!(
                $this->_isAllowedPolicy($policyRead) || $this->_isAllowedPolicy($policyWrite)
            )) {
                unset($tmp[$module]["actions"]["search"]);
            }
        }
        $modules = $tmp;
    }

    private function _removeEmptyActions(array &$modules): void
    {
        $tmp = $modules;
        foreach ($tmp as $module => $config) {
            if (!$tmp[$module]["actions"]) {
                unset($tmp[$module]);
            }
        }
        $modules = $tmp;
    }

    private function _getFinalAllowedModules(): array
    {
        $modules = $this->modules;
        if ($this->authService->isAuthUserRoot() && !$this->allowedPolicies) {
            return $modules;
        }
        $this->_removeNoWriteableActions($modules);
        $this->_removeNoReadableActions($modules);
        $this->_removeEmptyActions($modules);
        return $modules;
    }

    public function __invoke(): array
    {
        return $this->_getFinalAllowedModules();
    }

    public function getMenuConfiguration(): array
    {
        $modules = $this->_getFinalAllowedModules();
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
