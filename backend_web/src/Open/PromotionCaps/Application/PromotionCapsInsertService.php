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
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Enums\ExceptionType;
use App\Picklist\Domain\Enums\AppArrayType;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapsInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private FieldsValidator $validator;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;
    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapUsersRepository $repopromocapuser;
    private ArrayRepository $repoarray;

    private array $promotion;
    private array $promotionui;

    public function __construct(array $input)
    {
        $this->_load_input($input);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
        $this->repoarray = RF::get(ArrayRepository::class);
    }

    private function _load_input(array $input): void
    {
        $tofks = [
            PromotionCapUserType::INPUT_GENDER,
            PromotionCapUserType::INPUT_LANGUAGE,
            PromotionCapUserType::INPUT_COUNTRY
        ];
        foreach ($input as $key=>$value) {
            $key = str_replace("input-", "", $key);
            if (in_array($key, $tofks)) $key = "id_$key";
            $this->input[$key] = $value;
        }
    }

    private function _load_promotion(): void
    {
        $promotionuuid = $this->input["_promotionuuid"];
        $this->promotion = $this->repopromotion->get_by_uuid($promotionuuid, [
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
                        : __("Wrong first name format. Only letters allowed.");
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME2) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::name_format($data["value"])
                        ? false
                        : __("Wrong last name format. Only letters allowed.");
                });
            }

            if ($field === PromotionCapUserType::INPUT_PHONE1) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return CheckerService::phone_format($data["value"])
                        ? false
                        : __("Wrong mobile format. Use only numbers and white space please");
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

            if ($field === PromotionCapUserType::INPUT_COUNTRY) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return $this->repoarray->exists((int)$data["value"], AppArrayType::COUNTRY, "id_pk")
                        ? false
                        : __("Unrecognized country");
                });
            }

            if ($field === PromotionCapUserType::INPUT_LANGUAGE) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return $this->repoarray->exists((int)$data["value"], AppArrayType::LANGUAGE, "id_pk")
                        ? false
                        : __("Unrecognized language");
                });
            }

            if ($field === PromotionCapUserType::INPUT_GENDER) {
                $this->validator->add_rule($field, "format", function ($data) {
                    return $this->repoarray->exists((int)$data["value"], AppArrayType::GENDER, "id_pk")
                        ? false
                        : __("Unrecognized gender");
                });
            }

        }//foreach

        //to-do pasr fks
        $toskip = array_diff($fields, PromotionCapUserType::get_all());
        $toskip = $toskip + ["uuid", "id_owner", "id_promotion"];
        foreach ($toskip as $skip)
            $this->validator->add_skip($skip);

        return $this->validator;
    }

    private function _map_entity(array &$promocapuser): void
    {
        $skip = $this->validator->get_skip();
        foreach ($skip as $field) unset($promocapuser[$field]);
        $promocapuser["uuid"] = uniqid();
        $promocapuser["id_owner"] = $this->promotion["id_owner"];
        $promocapuser["id_promotion"] = $this->promotion["id"];
    }

    public function __invoke(): array
    {
        $this->_load_request();
        if (!$promocapuser = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_load_promotion();
        $this->_load_promotionui();

        if ($errors = $this->_add_rules_by_ui()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $entitypromouser = MF::get(PromotionCapUsersEntity::class);
        $promocapuser = $entitypromouser->map_request($promocapuser);
        $this->_map_entity($promocapuser);
        $this->repopromocapuser->insert($promocapuser);
        return [
            "description" => __("You have successfully subscribed. Please check your email to confirm your subscription!")
        ];
    }
}