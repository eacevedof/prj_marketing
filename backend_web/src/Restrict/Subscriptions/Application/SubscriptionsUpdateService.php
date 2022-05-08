<?php
namespace App\Restrict\Subscriptions\Application;

use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Checker\Application\CheckerService;
use App\Restrict\Subscriptions\Domain\PromotionEntity;
use App\Restrict\Subscriptions\Domain\PromotionRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class SubscriptionsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;
    private TextComponent $textformat;
    private DateComponent $datecomp;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->datecomp = CF::get(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitypromotion = MF::get(PromotionEntity::class);
        $this->validator = VF::get($this->input, $this->entitypromotion);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotion->set_model($this->entitypromotion);
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get(TextComponent::class);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _map_dates(array &$input): void
    {
        $date = $input["date_from"] ?? "";
        $date = $this->datecomp->get_dbdt($date);
        $input["date_from"] = $date;
        $date = $input["date_to"] ?? "";
        $date = $this->datecomp->get_dbdt($date);
        $input["date_to"] = $date;
    }

    private function _check_entity_permission(array $promotion): void
    {
        if (!$this->repopromotion->get_id_by_uuid($uuid = $promotion["uuid"]))
            $this->_exception(
                __("{0} {1} does not exist", __("Promotion"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );

        if ($this->auth->is_system()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $promotion["id_owner"];
        //si el logado es propietario de la promocion
        if ($idauthuser===$identowner) return;
        //si el logado tiene el mismo owner que la promo
        if (RF::get(UserRepository::class)->get_idowner($idauthuser) === $identowner) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("uuid", "uuid", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_owner", "id_owner", function ($data) {
                //si no es de sistemas este campo no se puede cambiar
                if (!$this->auth->is_system()) return false;
                if (!($value = $data["value"])) return __("Empty field is not allowed");
                if (!RF::get(UserRepository::class)->is_owner((int) $value))
                    return __("Invalid owner");
                return false;
            })
            ->add_rule("description", "description", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("bgimage_xs", "bgimage_xs", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("bgimage_sm", "bgimage_sm", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("bgimage_md", "bgimage_md", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("bgimage_lg", "bgimage_lg", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("bgimage_xl", "bgimage_xl", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("bgimage_xxl", "bgimage_xxl", function ($data) {
                if (!$value = $data["value"]) return false;
                if (!CheckerService::is_valid_url($value)) return __("Invalid url format");
            })
            ->add_rule("date_from", "date_from", function ($data) {
                if (!$value = $data["value"]) return __("Empty field is not allowed");
                if (!$this->datecomp->set_date1($value)->is_valid()) return __("Invalid date {0}", $value);
                if ($value>$data["data"]["date_to"]) return __("Date from is greater than Date to");
                return false;
            })
            ->add_rule("date_to", "date_to", function ($data) {
                if (!$value = $data["value"]) return __("Empty field is not allowed");
                if (!$this->datecomp->set_date1($value)->is_valid()) return __("Invalid date {0}", $value);
                if ($value<$data["data"]["date_from"]) return __("Date to is lower than Date from");
                return false;
            })
            ->add_rule("id_tz", "id_tz", function ($data) {
                if (!$value = $data["value"]) return __("Empty field is not allowed");
                if (!RF::get(ArrayRepository::class)->get_timezone_description_by_id($value))
                    return __("Invalid timezone");
                return false;
            })
        ;
        return $this->validator;
    }

    private function _map_entity(array &$promotion): void
    {
        $utc = CF::get(UtcComponent::class);
        $tzfrom = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $promotion["id_tz"]);
        unset($promotion["slug"]);
        if (!$this->auth->is_system()) unset($promotion["id_owner"]);


        $promotion["slug"] = $this->textformat->set_text($promotion["description"])->slug()."-".$promotion["id"];
        $promotion["date_from"] = $utc->get_dt_into_tz($promotion["date_from"], $tzfrom);
        $promotion["date_to"] = $utc->get_dt_into_tz($promotion["date_to"], $tzfrom);

        unset($promotion["is_launched"]);
        if ($this->repopromotion->is_launched_by_uuid($promotion["uuid"])) {
            unset(
                $promotion["id_owner"], $promotion["description"], $promotion["description"], $promotion["slug"],
                $promotion["id_tz"], $promotion["date_from"], $promotion["date_to"], $promotion["is_raffleable"],
                $promotion["is_cumulative"], $promotion["content"]
            );
        }
        if ($promotion["is_published"]) $promotion["is_launched"] = 1;
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitypromotion->map_request($update);
        $this->_check_entity_permission($update);
        $this->_map_entity($update);
        $this->entitypromotion->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repopromotion->update($update);
        $promotion = $this->repopromotion->get_by_id($update["id"]);
        return [
            "affected" => $affected,
            "promotion" => [
                "id" => $promotion["id"],
                "uuid" => $promotion["uuid"],
                "is_launched" => $promotion["is_launched"],
                "slug" => $promotion["slug"],
                "is_published" => $promotion["is_published"],
            ]
        ];
    }
}