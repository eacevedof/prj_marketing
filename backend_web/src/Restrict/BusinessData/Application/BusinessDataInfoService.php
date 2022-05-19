<?php
namespace App\Restrict\BusinessData\Application;

use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class BusinessDataInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private UserRepository $repouser;
    private BusinessDataRepository $repobusinessdata;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->authuser = $this->auth->get_user();
        $this->repouser = RF::get(UserRepository::class);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_READ)
            || $this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(int $iduser): void
    {
        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $this->repouser->get_idowner($iduser);

        //si el owner logado es propietario de la entidad
        if ($this->auth->is_business_owner() && $idauthuser === $identowner)
            return;

        $idauthowner = $this->auth->get_idowner();
        if ($this->auth->is_business_manager() && $idauthowner === $identowner)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function get_for_edit_by_user(string $uuid): array
    {
        $this->_check_permission();
        if (!$id = $this->repouser->get_id_by_uuid($uuid))
            $this->_exception("{0} with code {1} not found", __("User"), $uuid);

        $this->_check_entity_permission($id);
        return $this->repobusinessdata->get_by_user($id);
    }

    public function get_by_id_user(int $id): array
    {
        $this->_check_permission();
        $this->_check_entity_permission($id);
        return $this->repobusinessdata->get_by_user($id);
    }

    public function get_slug_by_id_user(int $iduser): string
    {
        $r = $this->repobusinessdata->get_by_user($iduser, ["m.slug"]);
        return $r["slug"] ?? "";
    }
/*
    public function get_by_id_user_for_open(int $iduser): array
    {
        return $this->repobusinessdata->get_by_user($iduser, [
            "business_name","user_logo_1","user_logo_2","user_logo_3","url_favicon",
            "head_bgcolor","head_color","head_bgimage","body_bgcolor","body_color",
            "body_bgimage","url_business","url_social_fb","url_social_ig","url_social_twitter",
            "url_social_tiktok"
        ]);
    }
*/
}