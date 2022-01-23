<?php
namespace App\Services\Auth;

use App\Factories\Specific\SessionFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Enums\SessionType;
use App\Enums\ProfileType;

final class AuthService
{
    private static ?AuthService $authService = null;
    private static ?array $authuser = null;

    private function __construct() {}

    private function __clone() {}

    public static function getme(): self
    {
        if (!self::$authService)
            self::$authService = new AuthService();
        return self::$authService;
    }

    public function get_user(): ?array
    {
        if (!self::$authuser)
            self::$authuser = SF::get()->get(SessionType::AUTH_USER) ?? [];
        return self::$authuser;
    }

    public function is_user_allowed(string $action): bool
    {
        if(!self::$authuser) return false;
        if($this->is_root()) return true;

        $permissions = self::$authuser[SessionType::AUTH_USER_PERMISSIONS];
        return in_array($action, $permissions);
    }

    public function is_root(): bool
    {
        return ((self::$authuser["id_profile"] ?? "") === ProfileType::ROOT);
    }

    public function is_sysadmin(): bool
    {
        return ((self::$authuser["id_profile"] ?? "") === ProfileType::SYS_ADMIN);
    }

    public function is_business_owner(): bool
    {
        return ((self::$authuser["id_profile"] ?? "") === ProfileType::BUSINESS_OWNER);
    }

    public function is_business_manager(): bool
    {
        return ((self::$authuser["id_profile"] ?? "") === ProfileType::BUSINESS_MANAGER);
    }

    public function get_idowner(): ?int
    {
        if ($this->is_root() || $this->is_sysadmin())
            return null;
        if ($this->is_business_owner())
            return self::$authuser["id"];

        return RF::get("Base/User")->get_idowner(self::$authuser["id"]);
    }
}