<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapInfoService extends AppService  implements IEventDispatcher
{
    use RequestTrait;

    private BusinessDataRepository $repobusinessdata;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;

    private array $businesssdata;
    private array $promotion;
    private array $promotionui;
    private int $istest;

    public function __construct(array $input)
    {
        $this->_load_input($input);
        $this->istest = (int)($input["_test_mode"] ?? "");

        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
    }

    private function _load_input(array $input): void
    {
        foreach ($input as $k => $v)
            $this->input[$k] = trim($v);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _load_businessdata(): void
    {
        $businessslug = $this->input["businessslug"];
        $this->businesssdata = $this->repobusinessdata->get_by_slug($businessslug);
        if (!$this->businesssdata)
            $this->_promocap_exception(__("Business account {0} not found!", $businessslug), ExceptionType::CODE_NOT_FOUND);
    }

    private function _dispatch(): void
    {
        $this->_load_request();

        EventBus::instance()->publish(...[
            PromotionCapActionHasOccurredEvent::from_primitives(-1, [
                "id_promotion" => $this->promotion["id"] ?? -1,
                "id_promouser" => null,
                "id_type" => PromotionCapActionType::VIEWED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip(),
                "is_test" => $this->istest,
            ])
        ]);
    }

    private function _load_promotion(): void
    {
        $promotionslug = $this->input["promotionslug"];
        $this->promotion = $this->repopromotion->get_by_slug($promotionslug);
        $this->_dispatch();

        SF::get(PromotionCapCheckService::class, [
            "promotion" => $this->promotion,
            "is_test" => $this->istest,
            "user" => SF::get_auth()->get_user(),
        ])->is_suitable_or_fail();
    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->get_by_promotion((int) $this->promotion["id"]);
        if (!$this->promotionui)
            $this->_promocap_exception(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
    }

    public function __invoke(): array
    {
        if (!($this->input["businessslug"] ?? ""))
            $this->_promocap_exception(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);

        if (!($this->input["promotionslug"] ?? ""))
            $this->_promocap_exception(__("No promotion name provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->_load_businessdata();
        $this->_load_promotion();
        $this->_load_promotionui();

        return [
            "businessdata" => $this->businesssdata,
            "promotion" => $this->promotion,
            "promotionui" => $this->promotionui,

            "metadata" => [],
        ];
    }

}