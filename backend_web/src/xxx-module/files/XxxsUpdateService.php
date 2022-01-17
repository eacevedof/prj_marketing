<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\EntityFactory as MF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Factories\ServiceFactory as SF;
use App\Services\Auth\AuthService;
use App\Models\App\XxxEntity;
use App\Repositories\App\XxxRepository;
use App\Models\FieldsValidator;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;
use App\Exceptions\FieldsException;

final class XxxsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private XxxRepository $repoxxx;
    private FieldsValidator $validator;
    private XxxEntity $entityxxx;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entityxxx = MF::get("App/Xxx");
        $this->validator = VF::get($this->input, $this->entityxxx);
        $this->repoxxx = RF::get("App/XxxRepository");
        $this->repoxxx->set_model($this->entityxxx);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::XXXS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $idxxx = $this->repoxxx->get_id_by($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $idxxx) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repoxxx->get_ownerid($idxxx);
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
            %FIELD_RULES%
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

        $update = $this->entityxxx->map_request($update);
        $this->_check_entity_permission($update);
        if(!$update["secret"]) unset($update["secret"]);
        else
            $update["secret"] = $this->encdec->get_hashpassword($update["secret"]);
        $update["description"] = $update["fullname"];
        $this->entityxxx->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repoxxx->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}