<?php
namespace App\Services\Restrict\Users;
use App\Factories\RepositoryFactory;
use App\Factories\ValidatorFactory as VF;
use App\Models\Base\UserModel;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Enums\KeyType;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Factories\ModelFactory;
use App\Traits\RequestTrait;
use App\Enums\ExceptionType;
use App\Models\FieldsValidator;

final class UsersUpdateService extends AppService
{
    use SessionTrait;
    use RequestTrait;

    private array $user;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private FieldsValidator $validator;
    private UserModel $model;

    public function __construct(array $input)
    {
        $this->model = ModelFactory::get("Base/User");
        $this->validator = VF::get($input, $this->model);
        $this->repository = RepositoryFactory::get("Base/UserRepository");
        $this->repository->set_model($this->model);
        $this->_load_request($input);
        $this->user = $this->_sessioninit()->get(KeyType::AUTH_USER);
        $this->encdec = $this->_get_encdec();
    }

    private function _skip_validation(): self
    {
        $this->validator
            ->add_skip("password2")
        ;
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $repository = $this->repository;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repository){
                $email = trim($data["value"]);
                $uuid = $data["data"]["uuid"] ?? "";
                $id = $repository->get_id_by($uuid);
                if (!$id) return __("User with code {0} not found",$uuid);
                $idemail = $repository->email_exists($email);
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
        $update = $this->_get_without_operations();
        if (!$update)
            $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            $this->_exeption(__("Fields validation errors"), ExceptionType::CODE_BAD_REQUEST);
        }

        $update = $this->model->map_request($update);
        if(!$update["secret"]) unset($update["secret"]);
        else
            $update["secret"] = $this->encdec->get_hashpassword($update["secret"]);
        $update["description"] = $update["fullname"];
        $this->model->add_sysupdate($update, $this->user["id"]);

        $affected = $this->repository->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}