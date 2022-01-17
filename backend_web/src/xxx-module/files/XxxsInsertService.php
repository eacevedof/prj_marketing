<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Traits\RequestTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\ServiceFactory as  SF;
use App\Factories\EntityFactory as MF;
use App\Factories\Specific\ValidatorFactory as VF;
use App\Services\Auth\AuthService;
use App\Models\Base\XxxEntity;
use App\Repositories\Base\XxxRepository;
use App\Models\FieldsValidator;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;
use App\Exceptions\FieldsException;

final class XxxsInsertService extends AppService
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
        $this->entityxxx = MF::get("Base/Xxx");
        $this->validator = VF::get($this->input, $this->entityxxx);
        $this->repoxxx = RF::get("Base/Xxx");
        $this->repoprefs = RF::get("Base/XxxPreferences");
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
            %FIELD_RULES%
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
        $insert["uuid"] = uniqid();
        $this->entityxxx->add_sysinsert($insert, $this->authuser["id"]);
        $id = $this->repoxxx->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}