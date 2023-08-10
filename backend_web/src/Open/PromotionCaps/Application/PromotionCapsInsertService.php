<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Checker\Application\CheckerService;
use App\Picklist\Domain\Enums\AppArrayType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Promotions\Domain\{PromotionRepository, PromotionUiRepository};
use App\Open\PromotionCaps\Domain\Enums\{PromotionCapActionType, PromotionCapUserType};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};
use App\Open\PromotionCaps\Domain\Events\{PromotionCapActionHasOccurredEvent, PromotionCapUserSubscribedEvent};
use App\Open\PromotionCaps\Domain\{PromotionCapSubscriptionsRepository, PromotionCapUsersEntity, PromotionCapUsersRepository};

final class PromotionCapsInsertService extends AppService implements IEventDispatcher
{
    use RequestTrait;

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
        $this->istest = (int) ($input["_test_mode"] ?? 0);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
        $this->repopromotionui = RF::getInstanceOf(PromotionUiRepository::class);
        $this->reposubscription = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::getInstanceOf(PromotionCapUsersRepository::class);
        $this->repoarray = RF::getInstanceOf(ArrayRepository::class);
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
        foreach ($input as $key => $value) {
            $key = str_replace("input-", "", $key);
            if (in_array($key, $tofks)) {
                $key = "id_$key";
            }
            if (in_array($key, $bools)) {
                $value = $value === "1" ? 1 : 0;
            }
            $this->input[$key] = trim($value);
        }

        if (!key_exists("email", $this->input)) {
            $this->input["email"] = "";
        }
        if (!key_exists("is_terms", $this->input)) {
            $this->input["is_terms"] = "0";
        }
    }

    private function _load_promotion(): void
    {
        $promotionuuid = $this->input["_promotionuuid"];
        $this->promotion = $this->repopromotion->getEntityByEntityUuid($promotionuuid, [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "date_execution", "id_owner", "num_confirmed", "disabled_date"
        ]);

        SF::getInstanceOf(PromotionCapCheckService::class, [
            "email" => ($this->input["email"] ?? ""),
            "promotion" => $this->promotion,
            "is_test" => $this->istest,
            "user" => SF::getAuthService()->getAuthUserArray(),
        ])->isPromotionSuitableOrFail();
    }

    private function _load_promotionui(): void
    {
        $this->promotionui = $this->repopromotionui->getPromotionUiByIdPromotion($this->promotion["id"]);
        if (!$this->promotionui) {
            $this->_throwException(__("Missing promotion UI configuration!"), ExceptionType::CODE_FAILED_DEPENDENCY);
        }
    }

    private function _add_rules_by_ui(array $input): FieldsValidator
    {
        $promocapuser = MF::getInstanceOf(PromotionCapUsersEntity::class);
        $this->validator = VF::getFieldValidator($input, $promocapuser);

        $uifields = $this->repopromotionui->getActiveFieldsByIdPromotion($this->promotion["id"]);

        foreach ($uifields as $field) {
            if ($field === PromotionCapUserType::INPUT_EMAIL) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$data["value"]) {
                        return __("This field cannot be left blank");
                    }
                    if (!CheckerService::isValidEmail($data["value"])) {
                        return __("Invalid {0} format", __("Email"));
                    }
                })
                ->addRule($field, "exist", function ($data) {
                    $idpromotion = $this->promotion["id"];
                    $email = $data["value"] ?? "";
                    if ($this->repopromocapuser->isSubscribedByIdPromotionAndEmail($idpromotion, $email)) {
                        return __("You are already subscribed");
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME1) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$name1 = $data["value"]) {
                        return __("This field cannot be left blank");
                    }

                    if (!CheckerService::isNameFormatOk($name1)) {
                        return __("Invalid {0} format. Only letters allowed.", __("First name"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_NAME2) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$name2 = $data["value"]) {
                        return __("This field cannot be left blank");
                    }

                    if (!CheckerService::isNameFormatOk($name2)) {
                        return __("Invalid {0} format. Only letters allowed.", __("Last name"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_PHONE1) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$phone1 = $data["value"]) {
                        return __("This field cannot be left blank");
                    }

                    if (!CheckerService::isPhoneFormatOk($phone1)) {
                        return __("Invalid {0} format. Use only numbers and white space please", __("Mobile"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_ADDRESS) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$address = $data["value"]) {
                        return __("This field cannot be left blank");
                    }

                    if (!CheckerService::isAddressFormatOk($address)) {
                        return __("Invalid {0} format. Only letters allowed.", __("Address"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_BIRTHDATE) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$birthdate = $data["value"]) {
                        return __("This field cannot be left blank");
                    }

                    if (!CheckerService::isValidDate($birthdate)) {
                        return __("Invalid {0} value", __("Birthdate"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_COUNTRY) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$idcountry = ($data["data"]["id_country"] ?? "")) {
                        return __("This field cannot be left blank");
                    }

                    if (!$this->repoarray->exists((int) $idcountry, AppArrayType::COUNTRY)) {
                        return __("Unrecognized country");
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_LANGUAGE) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$idlanguage = ($data["data"]["id_language"] ?? "")) {
                        return __("This field cannot be left blank");
                    }

                    if (!$this->repoarray->exists((int) $idlanguage, AppArrayType::LANGUAGE, "id_pk")) {
                        return __("Unrecognized language");
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_GENDER) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!$idgender = ($data["data"]["id_gender"] ?? "")) {
                        return __("This field cannot be left blank");
                    }

                    if (!$this->repoarray->exists((int) $idgender, AppArrayType::GENDER, "id_pk")) {
                        return __("Unrecognized gender");
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_IS_MAILING) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!CheckerService::isBoolean($data["value"])) {
                        return __("Invalid {0} format. Only 0 or 1 allowed", __("Mailing"));
                    }
                });
            }

            if ($field === PromotionCapUserType::INPUT_IS_TERMS) {
                $this->validator->addRule($field, "format", function ($data) {
                    if (!CheckerService::isBoolean($isterms = $data["value"])) {
                        return __("Invalid {0} format. Only 0 or 1 allowed", __("Terms"));
                    }
                    if (!$isterms) {
                        return __("In order to finish your subscription you have to read and accept terms and conditions");
                    }
                });
            }
        }

        //to-do pasr fks
        $toskip = array_diff(PromotionCapUserType::getAllPromotionCapUserTypes(), $uifields);
        //pq aqui meto todo para que no se valide contra la bd?
        $toskip = array_merge($toskip, ["uuid", "id_country","id_language", "id_owner", "id_promotion", "id_gender"]);
        foreach ($toskip as $skip) {
            $this->validator->addSkipableField($skip);
        }

        return $this->validator;
    }

    private function _map_entity(array &$promocapuser): void
    {
        $skip = $this->validator->getSkippAbleFields();
        foreach ($skip as $field) {
            unset($promocapuser[$field]);
        }
        $promocapuser["uuid"] = "us".uniqid();
        $promocapuser["id_owner"] = $this->promotion["id_owner"];
        $promocapuser["id_promotion"] = $this->promotion["id"];
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            PromotionCapUserSubscribedEvent::fromPrimitives($idcapuser = $payload["idcapuser"], $payload["promocapuser"]),
            PromotionCapActionHasOccurredEvent::fromPrimitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $idcapuser,
                "id_type" => PromotionCapActionType::SUBSCRIBED,
                "url_req" => $this->requestComponent->getRequestUri(),
                "url_ref" => $this->requestComponent->getReferer(),
                "remote_ip" => $this->requestComponent->getRemoteIp(),
                "is_test" => $this->istest,
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_loadRequestComponentInstance();
        if (!$promocapuser = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->_load_promotion();
        $this->_load_promotionui();

        if ($errors = $this->_add_rules_by_ui($promocapuser)->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $entitypromouser = MF::getInstanceOf(PromotionCapUsersEntity::class);
        $promocapuser = $entitypromouser->getAllKeyValueFromRequest($promocapuser);
        $this->_map_entity($promocapuser);
        $idcapuser = $this->repopromocapuser->insert($promocapuser);

        $promocapuser["remote_ip"] = $this->requestComponent->getRemoteIp();
        $promocapuser["date_subscription"] = date("Y-m-d H:i:s");
        $promocapuser["is_test"] = $this->istest;

        $this->_dispatchEvents(["idcapuser" => $idcapuser, "promocapuser" => $promocapuser]);

        return [
            "description" => __("You have successfully subscribed. Please check your email to confirm your subscription!")
        ];
    }
}
