<?php
namespace App\Services\Restrict\Users;

use App\Enums\PolicyType;
use App\Enums\PreferenceType;
use App\Exceptions\FieldsException;
use App\Services\AppService;
use App\Services\Auth\AuthService;
use App\Traits\RequestTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\ServiceFactory as  SF;
use App\Factories\EntityFactory as MF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Models\Base\UserEntity;
use App\Repositories\Base\UserRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;

use App\Enums\ExceptionType;
use App\Models\FieldsValidator;

final class UsersInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repouser;
    private FieldsValidator $validator;
    private UserEntity $entityuser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();
        $this->input = $input;
        $this->entityuser = MF::get("Base/User");
        $this->validator = VF::get($this->input, $this->entityuser);
        $this->repouser = RF::get("Base/User");
        $this->repoprefs = RF::get("Base/UserPreferences");
        $this->authuser = $this->auth->get_user();
        $this->encdec = $this->_get_encdec();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::USERS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
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
        $repouser = $this->repouser;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repouser){
                return $repouser->email_exists($data["value"]) ? __("This email already exists"): false;
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
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entityuser->map_request($insert);
        $insert["secret"] = $this->encdec->get_hashpassword($insert["secret"]);
        $insert["description"] = $insert["fullname"];
        $insert["uuid"] = uniqid();
        $this->entityuser->add_sysinsert($insert, $this->authuser["id"]);

        $id = $this->repouser->insert($insert);
        $prefs = [
            "id_user" => $id,
            "pref_key" => PreferenceType::URL_DEFAULT_MODULE,
            "pref_value" => "/restrict"
        ];

        $this->entityuser->add_sysinsert($prefs, $this->authuser["id"]);
        $this->repoprefs->insert($prefs);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}