<?php
namespace App\Open\PromotionCaps\Application;

use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Components\Date\DateComponent;

final class PromotionCapsInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private BusinessDataRepository $repobusinessdata;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;

    private array $businesssdata;
    private array $promotion;
    private array $promotionui;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
        //$this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->_load_request();
    }


    private function _load_promotion(): void
    {
        $promotionuuid = $this->input["_promotionuuid"];
        $this->promotion = $this->repopromotion->get_by_uuid($promotionuuid, [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz", "date_from", "date_to"
        ]);

        if (!$this->promotion || $this->promotion["delete_date"])
            $this->_exception(__("Sorry but this promotion does not exist"), ExceptionType::CODE_NOT_FOUND);

        if (!$this->promotion["is_published"])
            $this->_exception(__("This promotion is paused"), ExceptionType::CODE_UNAUTHORIZED);

        $utc = new UtcComponent();
        $promotz = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $this->promotion["id_tz"]);
        $utcfrom = $utc->get_dt_into_tz($this->promotion["date_from"], $promotz);
        $utcto = $utc->get_dt_into_tz($this->promotion["date_to"], $promotz);
        $utcnow = $utc->get_dt_by_tz();
        $dt = new DateComponent();
        $seconds = $dt->get_seconds_between($utcfrom, $utcnow);
        if($seconds<0)
            $this->_exception(__("Sorry but this promotion has not started yet"));
        $seconds = $dt->get_seconds_between($utcnow, $utcto);
        if($seconds<0)
            $this->_exception(__("Sorry but this promotion has finished"));

    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->get_by_promotion((int) $this->promotion["id"]);
        if (!$this->promotionui)
            $this->_exception(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
    }

    private function _validate(): void
    {
        //que este publicada
        //que el email no este suscrito
        //que este en hora

    }

    public function __invoke(): array
    {
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