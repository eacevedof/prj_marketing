<?php
namespace App\Restrict\Promotions\Application;

use App\Checker\Application\CheckerService;
use App\Restrict\Promotions\Domain\Events\PromotionWasCreatedEvent;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as  SF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionEntity;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Picklist\Domain;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionsInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;
    private TextComponent $textformat;
    private DateComponent $datecomp;
    private ArrayRepository $repoapparray;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->datecomp = CF::get(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $input;
        $this->entitypromotion = MF::get(PromotionEntity::class);
        $this->validator = VF::get($this->input, $this->entitypromotion);

        $this->repopromotion = RF::get(PromotionRepository::class)->set_model($this->entitypromotion);
        $this->repoapparray = RF::get(ArrayRepository::class);
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get(TextComponent::class);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE))
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
        $input["tags"] = $input["tags"] = CF::get(TextComponent::class)->get_csv_cleaned($input["tags"]);
    }

    private function _skip_validation(): self
    {
        $this->validator->add_skip("is_published")->add_skip("is_launched");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id_owner", "empty", function ($data) {
                if ($this->auth->is_system() && !trim($data["value"]))
                    return __("Empty field is not allowed");
                return false;
            })
            ->add_rule("description", "empty", function ($data) {
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
            ->add_rule("id_tz", "id_tz", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
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
            });

        return $this->validator;
    }

    private function _map_entity(array &$promotion): void
    {
        if (!$this->auth->is_system()) $promotion["id_owner"] = $this->auth->get_idowner();
        $this->entitypromotion->add_sysinsert($promotion, $this->authuser["id"]);
        $promotion["uuid"] = uniqid();
        unset(
            $promotion["slug"], $promotion["is_published"],$promotion["is_launched"],$promotion["slug"],$promotion["is_raffleable"],
            $promotion["is_cumulative"], $promotion["max_confirmed"], $promotion["invested"], $promotion["returned"], $promotion["date_execution"]
        );
        $promotion["slug"] = $this->textformat->set_text($promotion["description"])->slug();
        $utc = CF::get(UtcComponent::class);
        $tzfrom = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $promotion["id_tz"]);
        //paso fechas a utc
        $promotion["date_from"] = $utc->get_dt_into_tz($promotion["date_from"], $tzfrom);
        $promotion["date_to"] = $utc->get_dt_into_tz($promotion["date_to"], $tzfrom);
        $promotion["date_execution"] =
    }

    public function __invoke(): array
    {
        if (!$insert = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entitypromotion->map_request($insert);
        $this->_map_entity($insert);
        $id = $this->repopromotion->insert($insert);
        $this->repopromotion->update_slug_with_id($id);
        $promotion = $this->repopromotion->get_by_id($id);
        EventBus::instance()->publish(...[
            PromotionWasCreatedEvent::from_primitives($id, $promotion)
        ]);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}