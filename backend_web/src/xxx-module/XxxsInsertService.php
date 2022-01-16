<?php
namespace App\Services\Restrict\Xxxs;

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
use App\Models\Base\XxxEntity;
use App\Repositories\Base\XxxRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;

use App\Enums\ExceptionType;
use App\Models\FieldsValidator;

final class XxxsInsertService extends AppService
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
        $this->entityxxx = MF::get("Base/Xxx");
        $this->validator = VF::get($this->input, $this->entityxxx);
        $this->repoxxx = RF::get("Base/Xxx");
        $this->repoprefs = RF::get("Base/XxxPreferences");
        $this->authxxx = $this->auth->get_xxx();
        $this->encdec = $this->_get_encdec();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_xxx_allowed(PolicyType::USERS_WRITE))
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
        $repoxxx = $this->repoxxx;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repoxxx){
                return $repoxxx->email_exists($data["value"]) ? __("This email already exists"): false;
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

        $insert = $this->entityxxx->map_request($insert);
        $insert["secret"] = $this->encdec->get_hashpassword($insert["secret"]);
        $insert["description"] = $insert["fullname"];
        $insert["uuid"] = uniqid();
        $this->entityxxx->add_sysinsert($insert, $this->authxxx["id"]);

        $id = $this->repoxxx->insert($insert);
        $prefs = [
            "id_xxx" => $id,
            "pref_key" => PreferenceType::URL_DEFAULT_MODULE,
            "pref_value" => "/restrict"
        ];

        $this->entityxxx->add_sysinsert($prefs, $this->authxxx["id"]);
        $this->repoprefs->insert($prefs);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}