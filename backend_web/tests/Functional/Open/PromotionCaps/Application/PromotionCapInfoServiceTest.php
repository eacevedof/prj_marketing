<?php

namespace Tests\Functional\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Restrict\Promotions\Domain\{PromotionRepository, PromotionUiRepository};
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionCapInfoServiceTest extends AppService implements IEventDispatcher
{
    use RequestTrait;

    private BusinessDataRepository $businessDataRepository;
    private PromotionRepository $promotionRepository;
    private PromotionUiRepository $promotionUiRepository;

    private array $businessData;
    private array $promotion;
    private array $promotionUi;
    private int $isTestMode;

    public function __construct(array $input)
    {
        $this->_loadInput($input);
        $this->isTestMode = (int) ($input["_test_mode"] ?? "");

        $this->businessDataRepository = RF::getInstanceOf(BusinessDataRepository::class);
        $this->promotionRepository = RF::getInstanceOf(PromotionRepository::class);
        $this->promotionUiRepository = RF::getInstanceOf(PromotionUiRepository::class);
    }

    private function _loadInput(array $input): void
    {
        foreach ($input as $k => $v) {
            $this->input[$k] = trim($v);
        }
    }

    private function _promotionCapException(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _loadBusinessData(): void
    {
        $businessSlug = $this->input["businessslug"];
        $this->businessData = $this->businessDataRepository->getBusinessDataByBusinessDataSlug($businessSlug);
        if (!$this->businessData) {
            $this->_promotionCapException(__("Business account {0} not found!", $businessSlug), ExceptionType::CODE_NOT_FOUND);
        }
    }

    private function _dispatchEvents(): void
    {
        $this->_loadRequestComponentInstance();

        EventBus::instance()->publish(...[
            PromotionCapActionHasOccurredEvent::fromPrimitives(-1, [
                "id_promotion" => $this->promotion["id"] ?? -1,
                "id_promouser" => null,
                "id_type" => PromotionCapActionType::VIEWED,
                "url_req" => $this->requestComponent->getRequestUri(),
                "url_ref" => $this->requestComponent->getReferer(),
                "remote_ip" => $this->requestComponent->getRemoteIp(),
                "is_test" => $this->isTestMode,
            ])
        ]);
    }

    private function _loadPromotionByPromotionSlug(): void
    {
        $promotionSlug = $this->input["promotionslug"];
        $this->promotion = $this->promotionRepository->getPromotionByPromotionSlug($promotionSlug);
        $this->_dispatchEvents();

        SF::getInstanceOf(PromotionCapCheckService::class, [
            "promotion" => $this->promotion,
            "is_test" => $this->isTestMode,
            "user" => SF::getAuthService()->getAuthUserArray(),
        ])->isPromotionSuitableOrFail();
    }

    private function _loadPromotionUiByPromotionId(): void
    {
        $this->promotionUi = $this->promotionUiRepository->getPromotionUiByIdPromotion((int) $this->promotion["id"]);
        if (!$this->promotionUi) {
            $this->_promotionCapException(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
        }
    }

    public function __invoke(): array
    {
        if (!($this->input["businessslug"] ?? "")) {
            $this->_promotionCapException(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!($this->input["promotionslug"] ?? "")) {
            $this->_promotionCapException(__("No promotion name provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->_loadBusinessData();
        $this->_loadPromotionByPromotionSlug();
        $this->_loadPromotionUiByPromotionId();

        return [
            "businessdata" => $this->businessData,
            "promotion" => $this->promotion,
            "promotionui" => $this->promotionUi,

            "metadata" => [],
        ];
    }

}
