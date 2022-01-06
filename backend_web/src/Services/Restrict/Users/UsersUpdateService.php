<?php
namespace App\Services\Restrict\Users;

use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\ModelFactory as MF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Factories\ServiceFactory as SF;
use App\Models\Base\UserModel;
use App\Repositories\Base\UserRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Models\FieldsValidator;
use App\Enums\ExceptionType;

final class UsersUpdateService extends AppService
{
    use RequestTrait;

    private array $authuser;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repouser;
    private FieldsValidator $validator;
    private UserModel $modeluser;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->modeluser = MF::get("Base/User");
        $this->validator = VF::get($this->input, $this->modeluser);
        $this->repouser = RF::get("Base/UserRepository");
        $this->repouser->set_model($this->modeluser);
        $this->authuser = SF::get_auth()->get_user();
        $this->encdec = $this->_get_encdec();
    }

    private function _skip_validation(): self
    {
        $this->validator->add_skip("password2");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $repouser = $this->repouser;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repouser){
                $email = trim($data["value"]);
                $uuid = $data["data"]["uuid"] ?? "";
                $id = $repouser->get_id_by($uuid);
                if (!$id) return __("User with code {0} not found",$uuid);
                $idemail = $repouser->email_exists($email);
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
        if (!$update)
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            $this->_exception(__("Fields validation errors"), ExceptionType::CODE_BAD_REQUEST);
        }

        $update = $this->modeluser->map_request($update);
        if(!$update["secret"]) unset($update["secret"]);
        else
            $update["secret"] = $this->encdec->get_hashpassword($update["secret"]);
        $update["description"] = $update["fullname"];
        $this->modeluser->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repouser->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}