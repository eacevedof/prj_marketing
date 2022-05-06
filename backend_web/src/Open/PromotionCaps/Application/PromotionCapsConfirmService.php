<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionWasExecutedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersEntity;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapsConfirmService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
    private PromotionCapSubscriptionsRepository $repopromocapsubscription;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $promotion;
    private array $subscriptiondata;

    public function __construct(array $input)
    {
        //"promotionuuid" => $promotionuuid,
        //"subscriptionuuid" => $subscriptionuuid
        $this->input = $input;
        $this->repopromocapsubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $this->promotion = $this->repopromotion->get_by_uuid($this->input["promotionuuid"], [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "id_owner"
        ]);

        SF::get(
            PromotionCapCheckService::class,
            [
                "email" => ($this->input["email"] ?? ""),
                "promotion" => $this->promotion,
            ]
        )
        ->is_suitable_or_fail();
    }

    private function _load_subscription(): void
    {
        $promosubscription = $this->repopromocapsubscription->get_by_uuid($this->input["subscriptionuuid"], ["id_promouser"]);
        if(!$promosubscription)
            $this->_promocap_exception(__("No subscription found"), ExceptionType::CODE_NOT_FOUND);

        $this->subscriptiondata = $this->repopromocapuser->get_data_for_mail($promosubscription["id_promouser"]);
        if (!$this->subscriptiondata)
            $this->_promocap_exception(__("No subscription data found"), ExceptionType::CODE_NOT_FOUND);

        if ($this->subscriptiondata["promocode"]!==$this->promotion["uuid"])
            $this->_promocap_exception(__("Promotion code does not match for this subscription"), ExceptionType::CODE_BAD_REQUEST);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    public function __invoke(): array
    {
        $this->_load_request();
        $this->_load_promotion();
        $this->_load_subscription();
        
        //$this->repopromocapsubscription->update()
    }
}