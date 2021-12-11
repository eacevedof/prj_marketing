<?php
namespace App\Services\Common;
use App\Enums\ProfileType;
use App\Services\AppService;
use App\Repositories\App\PicklistRepository;
use App\Factories\RepositoryFactory as RF;


final class PicklistService extends AppService
{
    private PicklistRepository $repository;

    public function __construct()
    {
        $this->repository = RF::get("App/Picklist");
    }

    public function get_countries(): array
    {
        return $this->repository->get_countries();
    }

    public function get_languages(): array
    {
        return $this->repository->get_languages();
    }

    public function get_profiles(): array
    {
        $profiles = $this->repository->get_profiles();

        if ($this->_get_auth()->is_root()) return $profiles;

        if ($this->_get_auth()->is_business_owner() || $this->_get_auth()->is_business_manager()) {
            $profiles = array_filter($profiles, function ($profile) {
                return !in_array($profile["key"], [ProfileType::ROOT, ProfileType::SYS_ADMIN, ProfileType::BUSINESS_OWNER]);
            });
            return array_values($profiles);
        }

        if ($this->_get_auth()->is_sysadmin())
            $profiles = array_filter($profiles, function ($profile){
                return !in_array($profile["key"], [ProfileType::ROOT, ProfileType::SYS_ADMIN]) ;
            });

        return array_values($profiles);
    }

    public function get_users_by_profile(string $profileid): array
    {
        $users = $this->repository->get_users_by_profile($profileid);

        if ($this->_get_auth()->is_business_owner()) {
            $idparent = $this->_get_auth()->get_user()["id"];
            $users = array_filter($users, function ($user) use($idparent) {
                return in_array($user["key"], [$idparent,""]) ;
            });
            $users = array_values($users);
            return $users;
        }

        if ($this->_get_auth()->is_business_manager()) {
            $idparent = $this->_get_auth()->get_user()["id_parent"];
            $users = array_filter($users, function ($user) use($idparent) {
                return in_array($user["key"], [$idparent,""]) ;
            });
            $users = array_values($users);
            return $users;
        }

        return $users;
    }

    public function get_users(?string $notid=null): array
    {
        $users = $this->repository->get_users();
        if (!$notid)
            return $users;
        $idsuer = array_search($notid,$users);
        if($idsuer) unset($users[$idsuer]);
        return $users;
    }
}