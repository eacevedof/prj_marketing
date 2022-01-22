<?php
namespace App\Services\Common;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Services\Auth\AuthService;
use App\Repositories\App\PicklistRepository;
use App\Repositories\Base\ArrayRepository as BaseArray;
use App\Repositories\App\ArrayRepository as AppArray;
use App\Enums\ProfileType;

//todo quitar AppService
final class PicklistService extends AppService
{
    private PicklistRepository $repopicklist;
    private AppArray $repoapparray;
    private BaseArray $repobasearray;
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->repopicklist = RF::get("App/Picklist");
        $this->repobasearray = RF::get("Base/Array");
        $this->repoapparray = RF::get("App/Array");
    }

    public function get_countries(): array
    {
        return $this->repoapparray->get_countries();
    }

    public function get_languages(): array
    {
        return $this->repoapparray->get_languages();
    }

    public function get_profiles(): array
    {
        $profiles = $this->repobasearray->get_profiles();

        if ($this->auth->is_root()) return $profiles;

        if ($this->auth->is_business_owner() || $this->auth->is_business_manager()) {
            $profiles = array_filter($profiles, function ($profile) {
                return !in_array($profile["key"], [ProfileType::ROOT, ProfileType::SYS_ADMIN, ProfileType::BUSINESS_OWNER]);
            });
            return array_values($profiles);
        }

        if ($this->auth->is_sysadmin())
            $profiles = array_filter($profiles, function ($profile){
                return !in_array($profile["key"], [ProfileType::ROOT, ProfileType::SYS_ADMIN]) ;
            });

        return array_values($profiles);
    }

    public function get_promotions(): array
    {
        return $this->repoapparray->get_promotions(
            $this->auth->get_idowner()
        );
    }

    public function get_users_by_profile(string $profileid): array
    {
        $users = $this->repopicklist->get_users_by_profile($profileid);

        if ($this->auth->is_business_owner()) {
            $idparent = $this->auth->get_user()["id"];
            $users = array_filter($users, function ($user) use($idparent) {
                return in_array($user["key"], [$idparent,""]) ;
            });
            $users = array_values($users);
            return $users;
        }

        if ($this->auth->is_business_manager()) {
            $idparent = $this->auth->get_user()["id_parent"];
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
        $users = $this->repopicklist->get_users();
        if (!$notid)
            return $users;
        $idsuer = array_search($notid,$users);
        if($idsuer) unset($users[$idsuer]);
        return $users;
    }
}