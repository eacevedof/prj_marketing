<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Promotions\Domain\{PromotionRepository, PromotionUiRepository};

final class PromotionCapsService extends AppService
{
    private BusinessDataRepository $repobusinessdata;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;

    private array $businesssdata;
    private array $promotion;
    private array $promotionui;

    public function __construct(array $input)
    {
        if (!$input["businessslug"]) {
            $this->_throwException(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$input["promotionslug"]) {
            $this->_throwException(__("No promotion name provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->input = $input;

        $this->repobusinessdata = RF::getInstanceOf(BusinessDataRepository::class);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
        $this->repopromotionui = RF::getInstanceOf(PromotionUiRepository::class);
    }

    private function _load_businessdata(): void
    {
        $businessSlug = $this->input["businessslug"];
        $this->businesssdata = $this->repobusinessdata->getBusinessDataByBusinessDataSlug($businessSlug);
        if (!$this->businesssdata) {
            $this->_throwException(__("Business account {0} not found!", $businessSlug), ExceptionType::CODE_NOT_FOUND);
        }
    }

    private function _load_promotion(): void
    {
        $promotionslug = $this->input["promotionslug"];
        $this->promotion = $this->repopromotion->getPromotionByPromotionSlug($promotionslug);
        if (!$this->promotion) {
            $this->_throwException(__("{0} {1} not found!", __("Promotion"), $promotionslug), ExceptionType::CODE_NOT_FOUND);
        }
    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->getPromotionUiByIdPromotion((int) $this->promotion["id"]);
        if (!$this->promotionui) {
            $this->_throwException(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
        }
    }

    public function __invoke(): array
    {
        $this->_load_businessdata();
        $this->_load_promotion();
        $this->_load_promotionui();

        return [
            "businessdata" => $this->businesssdata,
            "promotion" => $this->promotion,
            "promotionui" => $this->promotionui,

            "metadata" => [

            ], //depende si es test o no
        ];
    }
}
