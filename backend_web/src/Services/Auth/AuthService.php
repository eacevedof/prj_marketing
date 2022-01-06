<?php

namespace App\Services\Auth;
use App\Factories\Specific\SessionFactory as SF;
use App\Components\Session\SessionComponent;
use App\Enums\SessionType;
use App\Enums\ProfileType;

final class AuthService
{
    private SessionComponent $session;
    private static ?array $user = null;

    public function __construct()
    {
        $this->session = SF::get();
        if (!self::$user)
            self::$user = $this->session->get(SessionType::AUTH_USER) ?? [];
    }

    public function get_user(): ?array
    {
        return self::$user;
    }

    public function is_user_allowed(string $action): bool
    {
        if(!self::$user) return false;
        if($this->is_root()) return true;

        //$permissions = $this->session->get(SessionType::AUTH_USER_PERMISSIONS);
        $permissions = self::$user[SessionType::AUTH_USER_PERMISSIONS];
        return in_array($action, $permissions);
    }

    public function is_root(): bool
    {
        return ((self::$user["id_profile"] ?? "") === ProfileType::ROOT);
    }

    public function is_sysadmin(): bool
    {
        return ((self::$user["id_profile"] ?? "") === ProfileType::SYS_ADMIN);
    }

    public function is_business_owner(): bool
    {
        return ((self::$user["id_profile"] ?? "") === ProfileType::BUSINESS_OWNER);
    }

    public function is_business_manager(): bool
    {
        return ((self::$user["id_profile"] ?? "") === ProfileType::BUSINESS_MANAGER);
    }
}