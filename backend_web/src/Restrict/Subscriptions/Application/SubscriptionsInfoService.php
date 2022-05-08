<?php
namespace App\Restrict\Subscriptions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class SubscriptionsInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;

    private PromotionCapSubscriptionsRepository $repocapsubscription;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No {0} code provided", __("promotion")), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repocapsubscription = RF::get(PromotionCapSubscriptionsRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entitycapsubs): void
    {
        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $entitycapsubs["id_owner"];
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
        if(!$capsubscription = $this->repocapsubscription->get_info($this->input))
            $this->_exception(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($capsubscription);
        $this->_map_entity($capsubscription);
        return [
            "promotion" => $capsubscription
        ];
    }

    private function _map_entity(array &$capsubscription): void
    {

    }

    public function get_for_edit(): array
    {
        $capsubscription = $this->repocapsubscription->get_info($this->input);
        if(!$capsubscription)
            $this->_exception(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($capsubscription);
        $this->_map_entity($capsubscription);

        return [
            "subscription" => $capsubscription,
        ];
    }
}