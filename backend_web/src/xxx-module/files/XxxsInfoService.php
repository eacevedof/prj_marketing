<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Services\Auth\AuthService;
use App\Repositories\App\XxxRepository;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;

final class XxxsInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private XxxRepository $repoxxx;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No xxx code provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repoxxx = RF::get("Base/Xxx");
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
        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        $idauthuser = (int)$this->authuser["id"];
        $identowner = (int) $entity["id_owner"];
        //si el owner logado es propietario de la entidad
        if ($this->auth->is_business_owner() && $idauthuser === $identowner)
            return;

        //si el logado es bm y la ent es del mismo owner
        $idauthowner = (int)$this->authuser["id_owner"];
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
            "xxx" => $xxx
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
        return $xxx;
    }
}