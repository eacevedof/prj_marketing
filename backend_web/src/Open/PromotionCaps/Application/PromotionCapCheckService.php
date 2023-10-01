<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Services\AppService;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Shared\Infrastructure\Components\Date\{DateComponent, UtcComponent};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionCapCheckService extends AppService
{
    private string $email;
    private array $promotion;
    private int $isTestMode;
    private array $user;

    public function __construct(array $input)
    {
        $this->promotion = $input["promotion"] ?? [];
        $this->email = $input["email"] ?? "";
        $this->isTestMode = (int) ($input["is_test"] ?? 0);
        $this->user = $input["user"] ?? [];
    }

    private function _throwPromoCapException(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    public function isPromotionSuitableOrFail(): void
    {
        $promotion = $this->promotion;
        if (!$promotion || $promotion["delete_date"]) {
            $this->_throwPromoCapException(__("Sorry but this promotion does not exist"), ExceptionType::CODE_NOT_FOUND);
        }

        if (SF::getInstanceOf(BusinessDataDisabledService::class)($promotion["id_owner"])) {
            $this->_throwPromoCapException(__("Sorry but this promotion is paused (1)"));
        }

        if ($promotion["disabled_date"]) {
            $this->_throwPromoCapException(__("Sorry but this promotion is disabled"), ExceptionType::CODE_LOCKED);
        }

        $promotion["id"] = (int) $promotion["id"];
        if (!($promotion["is_published"] || ($this->isTestMode && $this->user))) {
            $this->_throwPromoCapException(__("This promotion is paused"), ExceptionType::CODE_FORBIDDEN);
        }

        $utc = CF::getInstanceOf(UtcComponent::class);
        $dt = CF::getInstanceOf(DateComponent::class);

        $seconds = $dt->getSecondsBetween($nowTimeInUtc = $utc->getNowDtIntoTargetTz(), $promotion["date_execution"]);
        if ($seconds < 0) {
            $this->_throwPromoCapException(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }

        $seconds = $dt->getSecondsBetween($promotion["date_from"], $nowTimeInUtc);
        if ($seconds < 0) {
            $this->_throwPromoCapException(
                __("Sorry but this promotion has not started yet or is paused. Please try again later."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }

        $seconds = $dt->getSecondsBetween($nowTimeInUtc, $promotion["date_to"]);
        if ($seconds < 0) {
            $this->_throwPromoCapException(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }

        if ($promotion["max_confirmed"] != -1 && ($promotion["max_confirmed"] <= $promotion["num_confirmed"])) {
            $this->_throwPromoCapException(
                __("Sorry but this promotion has reached the max number of subscriptions"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }

        $email = trim($this->email ?? "");
        if ($email && RF::getInstanceOf(PromotionCapUsersRepository::class)->isSubscribedByIdPromotionAndEmail($promotion["id"], $email)) {
            $this->_throwPromoCapException(
                __("You are already subscribed. Please, check your inbox or spam folder"),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }
    }
}
