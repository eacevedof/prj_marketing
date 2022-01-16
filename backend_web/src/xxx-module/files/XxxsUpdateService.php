<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\EntityFactory as MF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Services\Auth\AuthService;
use App\Factories\ServiceFactory as SF;
use App\Models\Base\XxxEntity;
use App\Repositories\Base\XxxRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Exceptions\FieldsException;
use App\Models\FieldsValidator;
use App\Enums\ExceptionType;

final class XxxsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authxxx;
    private ComponentEncdecrypt $encdec;
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

        $this->entityxxx = MF::get("Base/Xxx");
        $this->validator = VF::get($this->input, $this->entityxxx);
        $this->repoxxx = RF::get("Base/XxxRepository");
        $this->repoxxx->set_model($this->entityxxx);
        $this->authxxx = $this->auth->get_user();
        $this->encdec = $this->_get_encdec();
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
        $idauthxxx = (int)$this->authxxx["id"];
        if ($this->auth->is_root() || $idauthxxx === $idxxx) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repoxxx->get_ownerid($idxxx);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $idauthxxx === $identowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skip_validation(): self
    {
        $this->validator->add_skip("password2");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $repoxxx = $this->repoxxx;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repoxxx){
                $email = trim($data["value"]);
                $uuid = $data["data"]["uuid"] ?? "";
                $id = $repoxxx->get_id_by($uuid);
                if (!$id) return __("Xxx with code {0} not found",$uuid);
                $idemail = $repoxxx->email_exists($email);
                if (!$idemail || ($id == $idemail)) return false;
                return __("This email already exists");
            })
            ->add_rule("email", "email", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("email", "email", function ($data) {
                return filter_var($data["value"], FILTER_VALIDATE_EMAIL) ? false : __("Invalid email format");
            })
            ->add_rule("phone", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("fullname", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("birthdate", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_profile", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_parent", "by-profile", function ($data){
                if (($data["data"]["id_profile"] ?? "") === "4" && !trim($data["value"]))
                    return __("Empty field is not allowed");
                return false;
            })
            ->add_rule("id_country", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_language", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("password", "not-equal", function ($data){
                if(!($password = trim($data["value"]))) return false;
                $password2 = trim($data["data"]["password2"] ?? "");
                return ($password === $password2) ? false : __("Bad password confirmation");
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

        $update = $this->entityxxx->map_request($update);
        $this->_check_entity_permission($update);
        if(!$update["secret"]) unset($update["secret"]);
        else
            $update["secret"] = $this->encdec->get_hashpassword($update["secret"]);
        $update["description"] = $update["fullname"];
        $this->entityxxx->add_sysupdate($update, $this->authxxx["id"]);

        $affected = $this->repoxxx->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}