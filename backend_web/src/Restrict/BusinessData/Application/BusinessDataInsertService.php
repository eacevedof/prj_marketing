<?php
namespace App\Restrict\BusinessData\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as  SF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataEntity;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class BusinessDataInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private BusinessDataRepository $repobusinessdata;
    private FieldsValidator $validator;
    private BusinessDataEntity $entitybusinessdata;
    private TextComponent $textformat;
    private ArrayRepository $repoapparray;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();
        
        $this->_map_dates($input);
        $this->input = $input;
        $this->entitybusinessdata = MF::get(BusinessDataEntity::class);
        $this->validator = VF::get($this->input, $this->entitybusinessdata);

        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repoapparray = RF::get(ArrayRepository::class);
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get(TextComponent::class);
    }

    private function _map_dates(array &$input): void
    {
        $dt = CF::get(DateComponent::class);
        $date = $input["date_from"] ?? "";
        $date = $dt->to_db($date);
        $input["date_from"] = $date;
        $date = $input["date_to"] ?? "";
        $date = $dt->to_db($date);
        $input["date_to"] = $date;
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _skip_validation(): self
    {
        $this->validator->add_skip("id");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("uuid", "uuid", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("id_user", "id_user", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("slug", "slug", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("user_logo_1", "user_logo_1", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("user_logo_2", "user_logo_2", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("user_logo_3", "user_logo_3", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_favicon", "url_favicon", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("head_bgcolor", "head_bgcolor", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("head_color", "head_color", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("head_bgimage", "head_bgimage", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("body_bgcolor", "body_bgcolor", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("body_color", "body_color", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("body_bgimage", "body_bgimage", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("site", "site", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_social_fb", "url_social_fb", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_social_ig", "url_social_ig", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_social_twitter", "url_social_twitter", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_social_tiktok", "url_social_tiktok", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
        ;
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

        $insert = $this->entitybusinessdata->map_request($insert);
        $insert["uuid"] = uniqid();
        $insert["slug"] = $this->textformat->set_text($insert["description"])->slug();
        if (!$this->auth->is_system()) $insert["id_owner"] = $this->auth->get_idowner();

        $this->entitybusinessdata->add_sysinsert($insert, $this->authuser["id"]);
        $id = $this->repobusinessdata->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}