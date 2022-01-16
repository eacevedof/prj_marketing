<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Services\Auth\AuthService;
use App\Repositories\Base\XxxRepository;
use App\Repositories\Base\XxxPermissionsRepository;
use App\Repositories\Base\XxxPreferencesRepository;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;

final class XxxsInfoService extends AppService
{
    private AuthService $auth;
    private array $authxxx;
    private XxxRepository $repoxxx;
    private XxxPermissionsRepository $repopermission;
    private XxxPreferencesRepository $repoprefs;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No xxx code provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->authxxx = $this->auth->get_user();
        $this->repoxxx = RF::get("Base/Xxx");
        $this->repopermission = RF::get("Base/XxxPermissions");
        $this->repoprefs = RF::get("Base/XxxPreferences");
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(PolicyType::XXXS_READ)
            || $this->auth->is_user_allowed(PolicyType::XXXS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $idxxx = (int) $entity["id"];
        $idauthxxx = (int)$this->authxxx["id"];
        if ($this->auth->is_root() || $idauthxxx === $idxxx) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::SYS_ADMIN, ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repoxxx->get_ownerid($idxxx);
        //si logado es propietario del bm
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $idauthxxx === $identowner
        )
            return;

        //si el logado es bm y la ent es del mismo owner
        $idauthowner = $this->repoxxx->get_ownerid($idauthxxx);
        if ($this->auth->is_business_manager() && $idauthowner === $identowner)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $xxx = $this->repoxxx->get_info($this->input);
        if(!$xxx)
            $this->_exception(
                __("Xxx with code {0} not found", $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($xxx);
        return [
            "xxx" => $xxx,
            "permissions" => $this->repopermission->get_by_xxx($idxxx = $xxx["id"]),
            "preferences" => $this->repoprefs->get_by_xxx($idxxx),
        ];
    }

    public function get_for_edit(): array
    {
        $xxx = $this->repoxxx->get_info($this->input);
        if(!$xxx)
            $this->_exception(
                __("Xxx with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($xxx);
        if($birthdate = $xxx["birthdate"]) $xxx["birthdate"] = substr($birthdate, 0,10);
        return $xxx;
    }
}