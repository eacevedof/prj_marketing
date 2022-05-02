<?php
namespace App\Open\PromotionCaps\Application;

use App\Checker\Application\CheckerService;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapUserType;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersEntity;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
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

    private FieldsValidator $validator;
    private BusinessDataRepository $repobusinessdata;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;
    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $businesssdata;
    private array $promotion;
    private array $promotionui;
    

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
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

        $this->promotion["id"] = (int) $this->promotion["id"];
        if (!$this->promotion["is_published"])
            $this->_exception(__("This promotion is paused"), ExceptionType::CODE_FORBIDDEN);

        $utc = new UtcComponent();
        $promotz = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $this->promotion["id_tz"]);
        $utcfrom = $utc->get_dt_into_tz($this->promotion["date_from"], $promotz);
        $utcto = $utc->get_dt_into_tz($this->promotion["date_to"], $promotz);
        $utcnow = $utc->get_dt_by_tz();
        $dt = new DateComponent();
        $seconds = $dt->get_seconds_between($utcfrom, $utcnow);
        if($seconds<0)
            $this->_exception(__("Sorry but this promotion has not started yet", ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS));
        $seconds = $dt->get_seconds_between($utcnow, $utcto);
        if($seconds<0)
            $this->_exception(__("Sorry but this promotion has finished", ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS));

        $this->promotion["max_confirmed"] = (int) $this->promotion["max_confirmed"];
        if($this->promotion["max_confirmed"] <= $this->reposubscription->get_num_confirmed($this->promotion["id"]))
            $this->_exception(__("Sorry but this promotion has reached the max number of subscriptions", ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS));

        $email = trim($this->input["email"] ?? "");
        if ($email && $this->repopromocapuser->is_subscribed_by_email($this->promotion["id"], $email))
            $this->_exception(__("Sorry but you can only subscribe once.", ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS));

    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->get_by_promotion($this->promotion["id"]);
        if (!$this->promotionui)
            $this->_exception(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
    }

    private function _add_rules_by_ui(): FieldsValidator
    {
        $promocapuser = MF::get(PromotionCapUsersEntity::class);
        $this->validator = VF::get($this->input, $promocapuser);

        $fields = $this->repopromotionui->get_active_fields($this->promotion["id"]);
        foreach ($fields as $field) {
            $this->validator->add_rule($field, "empty", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            });

            if ($field === PromotionCapUserType::INPUT_EMAIL) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::is_valid_email($data["value"])
                        ? false
                        : __("Wrong email format");
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME1) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::name_format($data["value"])
                        ? false
                        : __("Wrong first name format. (Only letters allowed)");
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME2) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::name_format($data["value"])
                        ? false
                        : __("Wrong last name format. Only letters allowed.");
                });
            }

            if ($field === PromotionCapUserType::INPUT_PHONE) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::name_format($data["value"])
                        ? false
                        : __("Wrong first name format. (Only letters allowed)");
                });
            }

            if ($field === PromotionCapUserType::INPUT_ADDRESS) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::address_format($data["value"])
                        ? false
                        : __("Wrong address format. Only letters allowed");
                });
            }

            if ($field === PromotionCapUserType::INPUT_BIRTHDATE) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::is_valid_date($data["value"])
                        ? false
                        : __("Wrong birthdate value");
                });
            }

        }

        $toskip = array_diff($fields, PromotionCapUserType::get_all());
        foreach ($toskip as $skip)
            $this->validator->add_skip($skip);

        return $this->validator;
    }
    public function __invoke(): array
    {
        $this->_load_promotion();
        $this->_load_promotionui();

        return [

        ];
    }
}