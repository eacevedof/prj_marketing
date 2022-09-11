<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\TimezoneType;
use App\Shared\Domain\Enums\ExceptionType;

final class PromotionsInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromoui;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No {0} code provided", __("promotion")), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromoui = RF::get(PromotionUiRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE)
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
        if(!$promotion = $this->repopromotion->get_info($this->input))
            $this->_exception(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($promotion);
        $this->_map_entity($promotion);
        return [
            "promotion" => $promotion
        ];
    }

    private function _map_entity(array &$promotion): void
    {
        $promotion["is_editable"] = !$this->repopromotion->has_subscribers_by_uuid($promotion["uuid"]);
        $promotz = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $promotion["id_tz"]);
        if ($promotz === TimezoneType::UTC) return;
        $utc = CF::get(UtcComponent::class);
        $promotion["date_from"] = $utc->get_dt_into_tz($promotion["date_from"], TimezoneType::UTC, $promotz);
        $promotion["date_to"] = $utc->get_dt_into_tz($promotion["date_to"], TimezoneType::UTC, $promotz);
        $promotion["date_execution"] = $utc->get_dt_into_tz($promotion["date_execution"], TimezoneType::UTC, $promotz);
        $promotion["date_raffle"] = $promotion["date_raffle"]
            ? $utc->get_dt_into_tz($promotion["date_raffle"], TimezoneType::UTC, $promotz)
            : null
        ;
    }

    public function get_for_edit(): array
    {
        $promotion = $this->repopromotion->get_info($this->input);
        if(!$promotion)
            $this->_exception(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($promotion);
        $this->_map_entity($promotion);

        $ispromotionui = $this->auth->get_module_permissions(
            UserPolicyType::MODULE_PROMOTIONS_UI, UserPolicyType::WRITE
        )[0];

        return [
            "promotion" => $promotion,
            "promotionui" => $ispromotionui
                ? $this->_get_with_sysdata(
                    $this->repopromoui->get_by_promotion((int) $promotion["id"]),
                    $this->auth->get_tz(),
                )
                : null,
            "raffle" => $promotion["date_raffle"]
                ? [
                    "promotion" => $promotion["description"],
                    "timezone" => RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $promotion["id_tz"]),
                    "date_raffle" => $promotion["date_raffle"],
                    "winners" => RF::get(PromotionCapUsersRepository::class)->get_raffle_winners(
                        (int) $promotion["id"],
                        ["m.id","m.uuid","m.name1", "m.email", "m.phone1"]
                    )
                ]
                : null
        ];
    }
}
//900 184 184
//91 377 97 79
//91 218 58 97