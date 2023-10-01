<?php

namespace App\Picklist\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Repositories\Base\ArrayRepository as BaseArray;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};
use App\Shared\Domain\Repositories\App\{ArrayRepository as AppArray, PicklistRepository};

//todo quitar AppService? mmm no creo el sf necesita ese tipo
final class PicklistService extends AppService
{
    private PicklistRepository $picklistRepository;
    private AppArray $appArrayRepository;
    private BaseArray $baseArrayRepository;
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->picklistRepository = RF::getInstanceOf(PicklistRepository::class);
        $this->baseArrayRepository = RF::getInstanceOf(BaseArray::class);
        $this->appArrayRepository = RF::getInstanceOf(AppArray::class);
    }

    public function getCountries(): array
    {
        return $this->appArrayRepository->getCountries();
    }

    public function getLanguages(): array
    {
        return $this->appArrayRepository->getLanguages();
    }

    public function getGenders(): array
    {
        return $this->appArrayRepository->getGenders();
    }

    public function getTimezones(): array
    {
        return $this->appArrayRepository->getTimezones();
    }

    public function getUserProfiles(): array
    {
        $profiles = $this->baseArrayRepository->getAllProfiles();

        if ($this->authService->isAuthUserRoot()) {
            return $profiles;
        }

        if ($this->authService->isAuthUserSysadmin()) {
            $profiles = array_filter($profiles, function ($profile) {
                $notin = [UserProfileType::ROOT];
                return !in_array($profile["key"], $notin);
            });
            return array_values($profiles);
        }

        if ($this->authService->isAuthUserBusinessOwner()) {
            $profiles = array_filter($profiles, function ($profile) {
                $notin = [UserProfileType::ROOT, UserProfileType::SYS_ADMIN];
                return !in_array($profile["key"], $notin);
            });
            return array_values($profiles);
        }

        //business manager
        $profiles = array_filter($profiles, function ($profile) {
            $notin = [UserProfileType::ROOT, UserProfileType::SYS_ADMIN, UserProfileType::BUSINESS_OWNER];
            return !in_array($profile["key"], $notin);
        });
        return array_values($profiles);
    }

    public function getPromotionTypes(): array
    {
        return $this->appArrayRepository->getPromotionTypesByIdOwner(
            $this->authService->getIdOwner()
        );
    }

    public function getUsersByProfile(string $profileId): array
    {
        $users = $this->picklistRepository->getUsersByIdProfile($profileId);

        if ($this->authService->isAuthUserBusinessOwner()) {
            $idParent = $this->authService->getAuthUserArray()["id"];
            $users = array_filter($users, function ($user) use ($idParent) {
                return in_array($user["key"], [$idParent,""]) ;
            });
            $users = array_values($users);
            return $users;
        }

        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $this->authService->getAuthUserArray()["id_parent"];
            $users = array_filter($users, function ($user) use ($idParent) {
                return in_array($user["key"], [$idParent,""]) ;
            });
            $users = array_values($users);
            return $users;
        }

        return $users;
    }

    public function getBusinessOwners(): array
    {
        return $this->picklistRepository->getAllBusinessOwners();
    }

    public function getUsers(?int $notIdUser = null): array
    {
        $users = $this->picklistRepository->getAllUsers();
        if (!$notIdUser) {
            return $users;
        }
        $idsuer = array_search($notIdUser, $users);
        if ($idsuer) {
            unset($users[$idsuer]);
        }
        return $users;
    }

    public function getNotOrYesOptions(array $conf = ["n" => 0, "y" => 1]): array
    {
        return [
            [ "key" => $conf["n"], "value" => __("No")],
            [ "key" => $conf["y"], "value" => __("Yes")],
        ];
    }
}
