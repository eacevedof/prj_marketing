<?php
namespace App\Services\Restrict\Promotions;

use App\Components\Date\DateComponent;
use App\Components\Formatter\TextComponent;
use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\ServiceFactory as  SF;
use App\Factories\EntityFactory as MF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Factories\ComponentFactory as CF;
use App\Services\Auth\AuthService;
use App\Models\App\PromotionEntity;
use App\Repositories\App\PromotionRepository;
use App\Models\FieldsValidator;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;
use App\Exceptions\FieldsException;

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

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();
        $this->input = $input;
        $this->entitypromotion = MF::get("App/Promotion");
        $this->validator = VF::get($this->input, $this->entitypromotion);
        $this->repopromotion = RF::get("App/Promotion");
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get("Formatter/Text");
        $this->datecomp = CF::get("Date/Date");
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _skip_validation(): self
    {
        //$this->validator->add_skip("id");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("description", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("content", "content", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_type", "id_type", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_from", "date_from", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_from", "date_from", function ($data) {
                return $this->datecomp->set_date1($date = $data["value"])->is_valid() ? false
                    : __("Invalid date {0}", $date);
            })
            ->add_rule("date_to", "date_to", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
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
                $url = trim($data["value"]);
                if (!$url) return false;
                return filter_var($url, FILTER_VALIDATE_URL) ? false : __("Invalid url");
            })
            ->add_rule("url_design", "url_design", function ($data) {
                $url = trim($data["value"]);
                if (!$url) return __("Empty field is not allowed");
                return filter_var($url, FILTER_VALIDATE_URL) ? false : __("Invalid url");
            })
            ->add_rule("is_active", "is_active", function ($data) {
                return in_array($data["value"], ["0","1"]) ? false: __("Invalid value");
            });

        return $this->validator;
    }

    public function __invoke(): array
    {
        $insert = $this->_get_req_without_ops($this->input);
        if (!$insert)
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entitypromotion->map_request($insert);
        $insert["uuid"] = uniqid();
        $insert["slug"] = $this->textformat->set_text($insert["description"])->slug();
        if (!$insert["id_owner"]) $insert["id_owner"] = $this->auth->get_idowner();
        $this->entitypromotion->add_sysinsert($insert, $this->authuser["id"]);
        $id = $this->repopromotion->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}