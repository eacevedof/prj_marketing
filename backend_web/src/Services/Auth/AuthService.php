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

    public function is_root(?string $idprofile=null): bool
    {
        return $idprofile
            ? ($idprofile === ProfileType::ROOT)
            : (self::$authuser["id_profile"] ?? "") === ProfileType::ROOT;
    }

    public function is_sysadmin(?string $idprofile=null): bool
    {
        return $idprofile
            ? ($idprofile === ProfileType::SYS_ADMIN)
            : (self::$authuser["id_profile"] ?? "") === ProfileType::SYS_ADMIN;
    }

    public function is_business_owner(?string $idprofile=null): bool
    {
        return $idprofile
            ? ($idprofile === ProfileType::BUSINESS_OWNER)
            : (self::$authuser["id_profile"] ?? "") === ProfileType::BUSINESS_OWNER;
    }

    public function is_business_manager(?string $idprofile=null): bool
    {
        return $idprofile
            ? ($idprofile === ProfileType::BUSINESS_MANAGER)
            : (self::$authuser["id_profile"] ?? "") === ProfileType::BUSINESS_MANAGER;
    }

    public function is_business(?string $idprofile=null): bool
    {
        $business = [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER];
        return $idprofile
            ? in_array($idprofile, $business)
            : in_array(self::$authuser["id_profile"] ?? "", $business);
    }

    public function is_system(?string $idprofile=null): bool
    {
        $system = [ProfileType::ROOT, ProfileType::SYS_ADMIN];
        return $idprofile
            ? in_array($idprofile, $system)
            : in_array(self::$authuser["id_profile"] ?? "", $system);
    }

    public function have_sameowner(string $idowner): bool
    {
        return $idowner === (string) $this->get_idowner();
    }

    public function get_idowner(): ?int
    {
        if ($this->is_root() || $this->is_sysadmin())
            return null;
        if ($this->is_business_owner())
            return self::$authuser["id"];

        return RF::get("Base/User")->get_idowner(self::$authuser["id"]);
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