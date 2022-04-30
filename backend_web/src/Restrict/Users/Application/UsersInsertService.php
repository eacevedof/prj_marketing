<?php
namespace App\Restrict\Users\Application;

use App\Restrict\Users\Domain\Events\UserWasCreated;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\UserEntity;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Bus\EventBus;

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

        $this->entityuser = MF::get(UserEntity::class);
        $this->repouser = RF::get(UserRepository::class)->set_model($this->entityuser);
        $this->repouserprefs = RF::get(UserPreferencesRepository::class);
        $this->authuser = $this->auth->get_user();
        $this->encdec = $this->_get_encdec();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::USERS_WRITE))
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

        $this->validator = VF::get($insert, $this->entityuser);
        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entityuser->map_request($insert);
        $insert["secret"] = $this->encdec->get_hashpassword($insert["secret"]);
        $insert["description"] = $insert["fullname"];
        $insert["uuid"] = uniqid();
        $this->entityuser->add_sysinsert($insert, $this->authuser["id"]);

        //save user
        $id = $this->repouser->insert($insert);
        $insert = $this->repouser->get_by_id((string) $id);
        EventBus::instance()->publish(...[
            UserWasCreated::from_primitives((int) $id, $insert)
        ]);

        $prefs = [
            "id_user" => $id,
            "pref_key" => UserPreferenceType::URL_DEFAULT_MODULE,
            "pref_value" => UrlType::RESTRICT
        ];

        $this->entityuser->add_sysinsert($prefs, $this->authuser["id"]);
        $this->repouserprefs->insert($prefs);

        $this->_load_request();
        $tz = CF::get(UtcComponent::class)->get_timezone_by_ip($this->request->get_remote_ip());
        $prefs = [
            "id_user" => $id,
            "pref_key" => UserPreferenceType::KEY_TZ,
            "pref_value" => $tz
        ];
        $this->repouserprefs->insert($prefs);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}