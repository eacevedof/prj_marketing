<?php
namespace App\Open\PromotionCaps\Application;

use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Enums\ExceptionType;

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
        if (!$input["businessslug"])
            $this->_exception(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);

        if (!$input["promotionslug"])
            $this->_exception(__("No promotion name provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->input = $input;

        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
    }

    private function _load_businessdata(): void
    {
        $businessslug = $this->input["businessslug"];
        $this->businesssdata = $this->repobusinessdata->get_by_slug($businessslug);
        if (!$this->businesssdata)
            $this->_exception(__("Business account {0} not found!", $businessslug), ExceptionType::CODE_NOT_FOUND);
    }

    private function _load_promotion(): void
    {
        $promotionslug = $this->input["promotionslug"];
        $this->promotion = $this->repopromotion->get_by_slug($promotionslug);
        if (!$this->promotion)
            $this->_exception(__("Promotion {$promotionslug} not found!"), ExceptionType::CODE_NOT_FOUND);
    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->get_by_promotion((int) $this->promotion["id"]);
        if (!$this->promotionui)
            $this->_exception(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
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