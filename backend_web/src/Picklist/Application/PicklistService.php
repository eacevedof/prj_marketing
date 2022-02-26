<?php
namespace App\Picklist\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\App\PicklistRepository;
use App\Shared\Domain\Repositories\Base\ArrayRepository as BaseArray;
use App\Shared\Domain\Repositories\App\ArrayRepository as AppArray;
use App\Restrict\Users\Domain\Enums\UserProfileType;

//todo quitar AppService? mmm no creo el sf necesita ese tipo
final class PicklistService extends AppService
{
    private PicklistRepository $repopicklist;
    private AppArray $repoapparray;
    private BaseArray $repobasearray;
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->repopicklist = RF::get(PicklistRepository::class);
        $this->repobasearray = RF::get(BaseArray::class);
        $this->repoapparray = RF::get(AppArray::class);
    }

    public function get_countries(): array
    {
        return $this->repoapparray->get_countries();
    }

    public function get_languages(): array
    {
        return $this->repoapparray->get_languages();
    }

    public function get_timezones(): array
    {
        return $this->repoapparray->get_timezones();
    }

    public function get_profiles(): array
    {
        $profiles = $this->repobasearray->get_profiles();

        if ($this->auth->is_root()) return $profiles;

        if ($this->auth->is_sysadmin()) {
            $profiles = array_filter($profiles, function ($profile){
                $notin = [UserProfileType::ROOT];
                return !in_array($profile["key"], $notin);
            });
            return array_values($profiles);
        }

        if ($this->auth->is_business_owner()) {
            $profiles = array_filter($profiles, function ($profile){
                $notin = [UserProfileType::ROOT, UserProfileType::SYS_ADMIN];
                return !in_array($profile["key"], $notin);
            });
            return array_values($profiles);
        }

        //business manager
        $profiles = array_filter($profiles, function ($profile){
            $notin = [UserProfileType::ROOT, UserProfileType::SYS_ADMIN, UserProfileType::BUSINESS_OWNER];
            return !in_array($profile["key"], $notin);
        });
        return array_values($profiles);
    }

    public function get_promotion_types(): array
    {
        return $this->repoapparray->get_promotion_types(
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

    public function get_not_or_yes(array $conf = ["n"=>"0", "y"=>"1"]): array
    {
        return [
            [ "key" => $conf["n"], "value" =>__("No")],
            [ "key" => $conf["y"], "value" =>__("Yes")],
        ];
    }
}