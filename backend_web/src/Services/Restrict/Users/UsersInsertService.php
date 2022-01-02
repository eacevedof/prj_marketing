<?php
namespace App\Services\Restrict\Users;

use App\Enums\PreferenceType;
use App\Enums\UrlType;
use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\ServiceFactory as  SF;
use App\Factories\ModelFactory as MF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Models\Base\UserModel;
use App\Repositories\Base\UserRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;

use App\Enums\ExceptionType;
use App\Models\FieldsValidator;

final class UsersInsertService extends AppService
{
    use RequestTrait;

    private array $user;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private FieldsValidator $validator;
    private UserModel $model;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->model = MF::get("Base/User");
        $this->validator = VF::get($this->input, $this->model);
        $this->repository = RF::get("Base/User");
        $this->preferences = RF::get("Base/UserPreferences");
        $this->user = SF::get("Auth/Auth")->get_user();
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
                return $repository->email_exists($data["value"]) ? __("This email already exists"): false;
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
            ->add_rule("password", "not-equal", function ($data){
                return ($data["value"] === ($data["data"]["password2"] ?? "")) ? false : __("Bad password confirmation");
            })
            ->add_rule("password", "empty", function ($data){
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
        ;
        return $this->validator;
    }

    public function __invoke(): array
    {
        $insert = $this->_get_req_without_ops($this->input);
        if (!$insert)
            $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            $this->_exeption(__("Fields validation errors"), ExceptionType::CODE_BAD_REQUEST);
        }

        $insert = $this->model->map_request($insert);
        $insert["secret"] = $this->encdec->get_hashpassword($insert["secret"]);
        $insert["description"] = $insert["fullname"];
        $insert["uuid"] = uniqid();
        $this->model->add_sysinsert($insert, $this->user["id"]);

        $id = $this->repository->insert($insert);
        $insert = [
            "id_user" => $id,
            "key" => PreferenceType::URL_DEFAULT_MODULE,
            "value" => "/restrict"
        ];
        $this->model->add_sysinsert($insert, $this->user["id"]);
        $this->preferences->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}