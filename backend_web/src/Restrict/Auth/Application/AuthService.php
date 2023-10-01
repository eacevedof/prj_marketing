<?php

namespace App\Restrict\Auth\Application;

use App\Shared\Domain\Enums\SessionType;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Factories\Specific\SessionFactory as SF;

final class AuthService
{
    private static ?AuthService $authService = null;
    private static ?array $authUserArray = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): self
    {
        if (!self::$authService) {
            self::$authService = new AuthService;
        }
        return self::$authService;
    }

    public function getAuthUserArray(): ?array
    {
        if (!self::$authUserArray) {
            self::$authUserArray = SF::get()->get(SessionType::AUTH_USER) ?? [];
        }
        return self::$authUserArray;
    }

    public function hasAuthUserPolicy(string $action): bool
    {
        if (!self::$authUserArray) {
            return false;
        }
        if ($this->isAuthUserRoot()) {
            return true;
        }

        $permissions = self::$authUserArray[SessionType::AUTH_USER_PERMISSIONS] ?? [];
        return in_array($action, $permissions);
    }

    public function getModulePermissions(string $module, ?string $rwAction = null): array
    {
        $permission = match ($module) {
            UserPolicyType::MODULE_USERS => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::USERS_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::USERS_READ),
            ],
            UserPolicyType::MODULE_USER_PERMISSIONS => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::USER_PERMISSIONS_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::USER_PERMISSIONS_READ),
            ],
            UserPolicyType::MODULE_USER_PREFERENCES => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::USER_PREFERENCES_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::USER_PREFERENCES_READ),
            ],
            UserPolicyType::MODULE_BUSINESSDATA => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::BUSINESSDATA_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::BUSINESSDATA_READ),
            ],
            UserPolicyType::MODULE_PROMOTIONS => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_READ),
            ],
            UserPolicyType::MODULE_PROMOTIONS_UI => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_UI_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_UI_READ),
            ],
            UserPolicyType::MODULE_SUBSCRIPTIONS => [
                "write" => $this->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE),
                "read" => $this->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_READ),
            ],
            default => [
                "write" => false,
                "read" => false,
            ],
        };

        if (!in_array($rwAction, [UserPolicyType::READ, UserPolicyType::WRITE])) {
            return $permission;
        }

        if ($rwAction === UserPolicyType::READ) {
            return [$permission["write"] || $permission["read"]];
        }

        return [$permission["write"]];
    }

    public function isAuthUserRoot(?int $idProfile = null): bool
    {
        return $idProfile
            ? ($idProfile === UserProfileType::ROOT)
            : ((int) (self::$authUserArray["id_profile"] ?? "")) === UserProfileType::ROOT;
    }

    public function isAuthUserSuperRoot(): bool
    {
        if (!self::$authUserArray) {
            return false;
        }
        return (
            self::$authUserArray["uuid"] === UserProfileType::ROOT_SUPER_UUID &&
            ((int) self::$authUserArray["id"]) === UserProfileType::ROOT_SUPER_ID
        );
    }

    public function isAuthUserSysadmin(?int $idProfile = null): bool
    {
        return $idProfile
            ? ($idProfile === UserProfileType::SYS_ADMIN)
            : ((int) (self::$authUserArray["id_profile"] ?? "")) === UserProfileType::SYS_ADMIN;
    }

    public function isAuthUserBusinessOwner(?int $idProfile = null): bool
    {
        return $idProfile
            ? ($idProfile === UserProfileType::BUSINESS_OWNER)
            : (self::$authUserArray["id_profile"] ?? null) === UserProfileType::BUSINESS_OWNER;
    }

    public function hasAuthUserBusinessManagerProfile(?int $idProfile = null): bool
    {
        return $idProfile
            ? ($idProfile === UserProfileType::BUSINESS_MANAGER)
            : ((int) (self::$authUserArray["id_profile"] ?? "")) === UserProfileType::BUSINESS_MANAGER;
    }

    public function isIdProfileBusinessProfile(?int $idProfile): bool
    {
        return in_array($idProfile, [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER]);
    }

    public function hasAuthUserSystemProfile(): bool
    {
        $system = [UserProfileType::ROOT, UserProfileType::SYS_ADMIN];
        return in_array((int) (self::$authUserArray["id_profile"] ?? ""), $system);
    }

    public function getIdOwner(): ?int
    {
        if ($this->isAuthUserRoot() || $this->isAuthUserSysadmin()) {
            return null;
        }

        if ($this->isAuthUserBusinessOwner()) {
            return self::$authUserArray["id"];
        }

        return RF::getInstanceOf(UserRepository::class)->getIdOwnerByIdUser(self::$authUserArray["id"]);
    }

    public function getAuthUserTZ(): string
    {
        //return "Europe/Madrid";
        return $this->getAuthUserArray()[SessionType::AUTH_USER_TZ] ?? "";
    }

    public static function reset(): void
    {
        self::$authService = null;
        self::$authUserArray = null;
    }
}
