<?php
namespace App\Restrict\BusinessData\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataEntity;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class BusinessDataUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private BusinessDataRepository $repobusinessdata;
    private FieldsValidator $validator;
    private BusinessDataEntity $entitybusinessdata;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitybusinessdata = MF::get(BusinessDataEntity::class);
        $this->validator = VF::get($this->input, $this->entitybusinessdata);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repobusinessdata->set_model($this->entitybusinessdata);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $idbusinessdata = $this->repobusinessdata->get_id_by_uuid($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $idbusinessdata) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repobusinessdata->get_idowner($idbusinessdata);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
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
            ->add_rule("slug", "slug", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("slug", "slug", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("user_logo_1", "user_logo_1", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("user_logo_2", "user_logo_2", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("user_logo_3", "user_logo_3", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
        ;
        return $this->validator;
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitybusinessdata->map_request($update);
        $this->_check_entity_permission($update);
        $this->entitybusinessdata->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repobusinessdata->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}