<?php
namespace App\Open\PromotionCaps\Application;

use App\Checker\Application\CheckerService;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapUserType;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersEntity;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Enums\ExceptionType;
use App\Picklist\Domain\Enums\AppArrayType;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapsInsertService extends AppService implements IEventDispatcher
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
    private int $istest;

    public function __construct(array $input)
    {
        $this->_load_input($input);
        $this->istest = (int)($input["_test_mode"] ?? 0);
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
        $bools = [
            PromotionCapUserType::INPUT_IS_TERMS,
            PromotionCapUserType::INPUT_IS_MAILING,
        ];
        foreach ($input as $key=>$value) {
            $key = str_replace("input-", "", $key);
            if (in_array($key, $tofks)) $key = "id_$key";
            if (in_array($key, $bools)) $value = $value==="1" ? 1 : 0;
            $this->input[$key] = trim($value);
        }

        if (!key_exists("email", $this->input)) $this->input["email"] = "";
        if (!key_exists("is_terms", $this->input)) $this->input["is_terms"] = "0";
    }

    private function _load_promotion(): void
    {
        $promotionuuid = $this->input["_promotionuuid"];
        $this->promotion = $this->repopromotion->get_by_uuid($promotionuuid, [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "date_execution", "id_owner", "num_confirmed", "disabled_date"
        ]);

        SF::get(PromotionCapCheckService::class, [
            "email" => ($this->input["email"] ?? ""),
            "promotion" => $this->promotion,
            "is_test" => $this->istest,
            "user" => SF::get_auth()->get_user(),
        ])->is_suitable_or_fail();
    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->get_by_promotion($this->promotion["id"]);
        if (!$this->promotionui)
            $this->_exception(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
    }

    private function _add_rules_by_ui(array $input): FieldsValidator
    {
        $promocapuser = MF::get(PromotionCapUsersEntity::class);
        $this->validator = VF::get($input, $promocapuser);

        $uifields = $this->repopromotionui->get_active_fields($this->promotion["id"]);

        foreach ($uifields as $field) {
            if ($field === PromotionCapUserType::INPUT_EMAIL) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$data["value"]) return __("Empty value is not allowed");
                    if (!CheckerService::is_valid_email($data["value"]))
                        return __("Wrong email format");
                })
                ->add_rule($field, "exist", function ($data) {
                    $idpromotion = $this->promotion["id"];
                    $email = $data["value"] ?? "";
                    if ($this->repopromocapuser->is_subscribed_by_email($idpromotion, $email))
                        return __("You are already subscribed");
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME1) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$name1 = $data["value"])
                        return __("Empty value is not allowed");

                    if (!CheckerService::name_format($name1))
                        return __("Wrong first name format. Only letters allowed.");
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME2) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$name2 = $data["value"])
                        return __("Empty value is not allowed");

                    if (!CheckerService::name_format($name2))
                        return __("Wrong last name format. Only letters allowed.");
                });
            }

            if ($field === PromotionCapUserType::INPUT_PHONE1) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$phone1 = $data["value"])
                        return __("Empty value is not allowed");

                    if (!CheckerService::phone_format($phone1))
                        return __("Wrong mobile format. Use only numbers and white space please");
                });
            }

            if ($field === PromotionCapUserType::INPUT_ADDRESS) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$address = $data["value"])
                        return __("Empty value is not allowed");

                    if (!CheckerService::address_format($address))
                        return __("Wrong address format. Only letters allowed");
                });
            }

            if ($field === PromotionCapUserType::INPUT_BIRTHDATE) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$birthdate = $data["value"])
                        return __("Empty value is not allowed");

                    if (!CheckerService::is_valid_date($birthdate))
                        return __("Wrong birthdate value");
                });
            }

            if ($field === PromotionCapUserType::INPUT_COUNTRY) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$idcountry = ($data["data"]["id_country"] ?? ""))
                        return __("Empty value is not allowed");

                    if (!$this->repoarray->exists((int)$idcountry, AppArrayType::COUNTRY))
                        return __("Unrecognized country");
                });
            }

            if ($field === PromotionCapUserType::INPUT_LANGUAGE) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$idlanguage = ($data["data"]["id_language"] ?? ""))
                        return __("Empty value is not allowed");

                    if (!$this->repoarray->exists((int)$idlanguage, AppArrayType::LANGUAGE, "id_pk"))
                        return __("Unrecognized language");
                });
            }

            if ($field === PromotionCapUserType::INPUT_GENDER) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!$idgender = ($data["data"]["id_gender"] ?? ""))
                        return __("Empty value is not allowed");

                    if (!$this->repoarray->exists((int)$idgender, AppArrayType::GENDER, "id_pk"))
                        return __("Unrecognized gender");
                });
            }

            if ($field === PromotionCapUserType::INPUT_IS_MAILING) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!CheckerService::is_boolean($data["value"]))
                        return __("Wrong mailing format. Only 0 or 1 allowed");
                });
            }

            if ($field === PromotionCapUserType::INPUT_IS_TERMS) {
                $this->validator->add_rule($field, "format", function ($data) {
                    if (!CheckerService::is_boolean($isterms = $data["value"]))
                        return __("Wrong mailing format. Only 0 or 1 allowed");
                    if (!$isterms)
                        return __("In order to finish your subscription you have to read and accept terms and conditions");
                });
            }
        }

        //to-do pasr fks
        $toskip = array_diff($uifields, PromotionCapUserType::get_all());
        $toskip = array_merge($toskip, ["uuid","id_country","id_language", "id_owner", "id_promotion", "id_gender", "is_mailing", "is_terms"]);
        foreach ($toskip as $skip)
            $this->validator->add_skip($skip);

        return $this->validator;
    }

    private function _map_entity(array &$promocapuser): void
    {
        $skip = $this->validator->get_skip();
        foreach ($skip as $field) unset($promocapuser[$field]);
        $promocapuser["uuid"] = "us".uniqid();
        $promocapuser["id_owner"] = $this->promotion["id_owner"];
        $promocapuser["id_promotion"] = $this->promotion["id"];
    }

    private function _dispatch(array $payload): void
    {
        EventBus::instance()->publish(...[
            PromotionCapUserSubscribedEvent::from_primitives($idcapuser = $payload["idcapuser"], $payload["promocapuser"]),
            PromotionCapActionHasOccurredEvent::from_primitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $idcapuser,
                "id_type" => PromotionCapActionType::SUBSCRIBED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip(),
                "is_test" => $this->istest,
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_load_request();
        if (!$promocapuser = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_load_promotion();
        $this->_load_promotionui();

        if ($errors = $this->_add_rules_by_ui($promocapuser)->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $entitypromouser = MF::get(PromotionCapUsersEntity::class);
        $promocapuser = $entitypromouser->map_request($promocapuser);
        $this->_map_entity($promocapuser);
        $idcapuser = $this->repopromocapuser->insert($promocapuser);

        $promocapuser["remote_ip"] = $this->request->get_remote_ip();
        $promocapuser["date_subscription"] = date("Y-m-d H:i:s");
        $promocapuser["is_test"] = $this->istest;

        $this->_dispatch(["idcapuser"=>$idcapuser, "promocapuser"=>$promocapuser]);

        return [
            "description" => __("You have successfully subscribed. Please check your email to confirm your subscription!")
        ];
    }
}