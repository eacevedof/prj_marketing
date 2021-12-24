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

    private function _exclude_write(array &$modules): void
    {

    }

    private function _exclude_read(array &$modules): void
    {

    }

    private function _exclude_empty(array &$modules): void
    {

    }
        
    public function __invoke(): array
    {
        $modules = $this->modules;
        $this->_exclude_write($modules);
        $this->_exclude_read($modules);
        $this->_exclude_empty($modules);
        return $modules;
    }
}