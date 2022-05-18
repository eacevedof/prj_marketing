<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapCheckService extends AppService
{
    private string $email;
    private array $promotion;
    private int $istest;
    private array $user;

    public function __construct(array $input)
    {
        $this->promotion = $input["promotion"] ?? [];
        $this->email = $input["email"] ?? "";
        $this->istest = (int)($input["is_test"] ?? 0);
        $this->user = $input["user"] ?? [];
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

        if (SF::get(BusinessDataDisabledService::class)($promotion["id_owner"]))
            $this->_promocap_exception(__("Sorry but this promotion is paused (1)"));

        if ($promotion["disabled_date"])
            $this->_promocap_exception(__("Sorry but this promotion is disabled"), ExceptionType::CODE_LOCKED);

        $promotion["id"] = (int) $promotion["id"];
        if (!($promotion["is_published"] || ($this->istest && $this->user)))
            $this->_promocap_exception(__("This promotion is paused"), ExceptionType::CODE_FORBIDDEN);

        $utc = CF::get(UtcComponent::class);
        $dt = CF::get(DateComponent::class);

        $seconds = $dt->get_seconds_between($utcnow = $utc->get_nowdt_in_timezone(), $promotion["date_execution"]);
        if($seconds<0)
            $this->_promocap_exception(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        $seconds = $dt->get_seconds_between($promotion["date_from"], $utcnow);
        if($seconds<0)
            $this->_promocap_exception(
                __("Sorry but this promotion has not started yet or is paused. Please try again later."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        $seconds = $dt->get_seconds_between($utcnow, $promotion["date_to"]);
        if($seconds<0)
            $this->_promocap_exception(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        if($promotion["max_confirmed"]!=-1 && ($promotion["max_confirmed"] <= $promotion["num_confirmed"]))
            $this->_promocap_exception(
                __("Sorry but this promotion has reached the max number of subscriptions"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );

        $email = trim($this->email ?? "");
        if ($email && RF::get(PromotionCapUsersRepository::class)->is_subscribed_by_email($promotion["id"], $email))
            $this->_promocap_exception(
                __("You are already subscribed. Check your subscription email"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
    }
}