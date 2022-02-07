<?php
namespace App\Restrict\Xxxs\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Xxxs\Domain\XxxRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

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
            $this->_exception(__("No {0} code provided", "xxx"), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repoxxx = RF::get(XxxRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::XXXS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::XXXS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $entity["id_owner"];
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

    public function __invoke(): array
    {
        if(!$xxx = $this->repoxxx->get_info($this->input))
            $this->_exception(
                __("{0} with code {1} not found", __("Xxx"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($xxx);
        return [
            "xxx" => $xxx
        ];
    }

    public function get_for_edit(): array
    {
        if(!$xxx = $this->repoxxx->get_info($this->input))
            $this->_exception(
                __("{0} with code {1} not found", __("Xxx"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($xxx);
        return $xxx;
    }
}