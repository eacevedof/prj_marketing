<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Services\AppService;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapCheckService extends AppService
{
    private string $email;
    private array $promotion;

    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapUsersRepository $repopromocapuser;

    public function __construct(array $input)
    {
        $this->promotion = $input["promotion"] ?? [];
        $this->email = $input["email"] ?? "";
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    public function is_suitable_or_fail(): void
    {
        $promotion = $this->promotion;
        if (!$promotion || $promotion["delete_date"])
            $this->_promocap_exception(__("Sorry but this promotion does not exist"), ExceptionType::CODE_NOT_FOUND);

        $promotion["id"] = (int) $promotion["id"];
        if (!$promotion["is_published"])
            $this->_promocap_exception(__("This promotion is paused"), ExceptionType::CODE_FORBIDDEN);

        $utc = CF::get(UtcComponent::class);
        $promotz = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $promotion["id_tz"]);
        $utcfrom = $utc->get_dt_into_tz($promotion["date_from"], $promotz);
        $utcto = $utc->get_dt_into_tz($promotion["date_to"], $promotz);
        $utcnow = $utc->get_nowdt_in_timezone();

        $dt = CF::get(DateComponent::class);
        $seconds = $dt->get_seconds_between($utcfrom, $utcnow);
        if($seconds<0)
            $this->_promocap_exception(
                __("Sorry but this promotion has not started yet or is paused. Please try again later."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        $seconds = $dt->get_seconds_between($utcnow, $utcto);
        if($seconds<0)
            $this->_promocap_exception(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        $promotion["max_confirmed"] = (int) $promotion["max_confirmed"];
        if($promotion["max_confirmed"]===0)
            $this->_promocap_exception(__("This promotion is disabled"), ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS);

        if($promotion["max_confirmed"]!=-1 && $promotion["max_confirmed"] <= $this->reposubscription->get_num_confirmed($promotion["id"]))
            $this->_promocap_exception(
                __("Sorry but this promotion has reached the max number of subscriptions"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        $email = trim($this->email ?? "");
        if ($email && $this->repopromocapuser->is_subscribed_by_email($promotion["id"], $email))
            $this->_promocap_exception(
                __("You are already subscribed. Check your subscription email"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
    }
}