<?php
namespace App\Services\Restrict\Promotions;

use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\EntityFactory as MF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Factories\ServiceFactory as SF;
use App\Services\Auth\AuthService;
use App\Models\App\PromotionEntity;
use App\Repositories\App\PromotionRepository;
use App\Models\FieldsValidator;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;
use App\Exceptions\FieldsException;

final class PromotionsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitypromotion = MF::get("App/Promotion");
        $this->validator = VF::get($this->input, $this->entitypromotion);
        $this->repopromotion = RF::get("App/PromotionRepository");
        $this->repopromotion->set_model($this->entitypromotion);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $idpromotion = $this->repopromotion->get_id_by($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $idpromotion) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repopromotion->get_idowner($idpromotion);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
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
            })->add_rule("id_owner", "id_owner", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("code_erp", "code_erp", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("description", "description", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("slug", "slug", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("content", "content", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("id_type", "id_type", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("date_from", "date_from", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("date_to", "date_to", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_social", "url_social", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("url_design", "url_design", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("is_active", "is_active", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("invested", "invested", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("returned", "returned", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })->add_rule("notes", "notes", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
        ;
        return $this->validator;
    }

    public function __invoke(): array
    {
        $update = $this->_get_req_without_ops($this->input);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitypromotion->map_request($update);
        $this->_check_entity_permission($update);
        $this->entitypromotion->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repopromotion->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}