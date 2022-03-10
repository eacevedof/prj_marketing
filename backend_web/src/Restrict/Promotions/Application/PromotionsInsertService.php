<?php
namespace App\Restrict\Promotions\Application;

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

        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repoapparray = RF::get(ArrayRepository::class);
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get(TextComponent::class);
    }

    private function _map_dates(array &$input): void
    {
        $date = $input["date_from"] ?? "";
        $date = $this->datecomp->set_date1($date)->explode(DateComponent::SOURCE_YMD)->to_db()->get();
        $input["date_from"] = $date;
        $date = $input["date_to"] ?? "";
        $date = $this->datecomp->set_date1($date)->explode(DateComponent::SOURCE_YMD)->to_db()->get();
        $input["date_to"] = $date;
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _skip_validation(): self
    {
        //$this->validator->add_skip("date_from")->add_skip("date_to");
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
            ->add_rule("content", "content", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_type", "id_type", function ($data) {
                $id_type = (int) $data["value"];
                if (!$id_type) return __("Empty field is not allowed");
                $i = $this->repoapparray->exists($id_type, AppArrayType::PROMOTION);
                if (!$i) return __("Invalid value");
                return false;
            })
            ->add_rule("date_from", "date_from", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_from", "date_from", function ($data) {
                return $this->datecomp->set_date1($date = $data["value"])->is_valid() ? false
                    : __("Invalid date {0}", $date);
            })
            ->add_rule("date_to", "date_to", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_to", "date_to", function ($data) {
                return $this->datecomp->set_date1($date = $data["value"])->is_valid() ? false
                    : __("Invalid date {0}", $date);
            })
            ->add_rule("date_to", "date_to", function ($data) {
                return ($this->datecomp->set_date1($data["data"]["date_from"])->set_date2($data["value"])->is_greater())
                    ? __("Date to should be larger or equal to date from.")
                    : false;
            })
            ->add_rule("url_social", "url_social", function ($data) {
                $url = $data["value"];
                if (!$url) return false;
                return filter_var($url, FILTER_VALIDATE_URL) ? false : __("Invalid url");
            })
            ->add_rule("url_design", "url_design", function ($data) {
                $url = $data["value"];
                if (!$url) return __("Empty field is not allowed");
                return filter_var($url, FILTER_VALIDATE_URL) ? false : __("Invalid url");
            })
            ->add_rule("is_published", "is_published", function ($data) {
                return in_array($data["value"], ["0","1"]) ? false: __("Invalid value");
            });

        return $this->validator;
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
        $insert["uuid"] = uniqid();
        $insert["slug"] = $this->textformat->set_text($insert["description"])->slug();
        if (!$this->auth->is_system()) $insert["id_owner"] = $this->auth->get_idowner();

        $this->entitypromotion->add_sysinsert($insert, $this->authuser["id"]);
        $id = $this->repopromotion->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}