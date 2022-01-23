<?php
namespace App\Services\Restrict\Promotions;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Services\Auth\AuthService;
use App\Repositories\App\PromotionRepository;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;

final class PromotionsInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No promotion code provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repopromotion = RF::get("App/Promotion");
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(PolicyType::PROMOTIONS_READ)
            || $this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE)
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
        $promotion = $this->repopromotion->get_info($this->input);
        if(!$promotion)
            $this->_exception(
                __("Promotion with code {0} not found", $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($promotion);
        return [
            "promotion" => $promotion
        ];
    }

    public function get_for_edit(): array
    {
        $promotion = $this->repopromotion->get_info($this->input);
        if(!$promotion)
            $this->_exception(
                __("Promotion with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($promotion);
        return $promotion;
    }
}