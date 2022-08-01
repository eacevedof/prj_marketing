<?php
namespace App\Restrict\Users\Application;

use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\UserEntity;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UsersInsertService extends AppService implements IEventDispatcher
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
                $value = $data["value"] ?? "";
                return $repouser->email_exists($value) ? __("This email already exists"): false;
            })
            ->add_rule("email", "email", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("email", "email", function ($data) {
                $value = $data["value"] ?? "";
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? false : __("Invalid email format");
            })
            ->add_rule("phone", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("fullname", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("birthdate", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("password", "not-equal", function ($data){
                $value = $data["value"] ?? "";
                return ($value === ($data["data"]["password2"] ?? "")) ? false : __("Bad password confirmation");
            })
            ->add_rule("password", "empty", function ($data){
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_profile", "empty", function ($data){
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_parent", "by-profile", function ($data){
                $value = $data["value"] ?? "";
                if (($data["data"]["id_profile"] ?? "") === "4" && !$value)
                    return __("Empty field is not allowed");
                return false;
            })
            ->add_rule("id_country", "empty", function ($data){
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_language", "empty", function ($data){
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
        ;
        return $this->validator;
    }

    private function _dispatch(array $payload): void
    {
        EventBus::instance()->publish(...[
            UserWasCreatedEvent::from_primitives($payload["id"], $payload["user"])
        ]);
    }

    public function __invoke(): array
    {
        $user = $this->_get_req_without_ops($this->input);
        if (!$user)
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->validator = VF::get($user, $this->entityuser);
        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $user = $this->entityuser->map_request($user);
        $user["secret"] = $this->encdec->get_hashpassword($user["secret"]);
        $user["description"] = $user["fullname"];
        $user["uuid"] = uniqid();
        $this->entityuser->add_sysinsert($user, $this->authuser["id"]);

        //save user
        $id = $this->repouser->insert($user);
        $user = $this->repouser->get_by_id((string) $id);

        $this->_dispatch(["id"=>$id,"user"=>$user]);

        return [
            "id" => $id,
            "uuid" => $user["uuid"]
        ];
    }
}