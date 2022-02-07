<?php
namespace App\Restrict\Xxxs\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as  SF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Xxxs\Domain\XxxEntity;
use App\Restrict\Xxxs\Domain\XxxRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class XxxsInsertService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private XxxRepository $repoxxx;
    private FieldsValidator $validator;
    private XxxEntity $entityxxx;
    private TextComponent $textformat;
    private DateComponent $datecomp;
    private ArrayRepository $repoapparray;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->datecomp = CF::get(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $input;
        $this->entityxxx = MF::get(XxxEntity::class);
        $this->validator = VF::get($this->input, $this->entityxxx);

        $this->repoxxx = RF::get(XxxRepository::class);
        $this->repoapparray = RF::get(ArrayRepository::class);
        $this->authuser = $this->auth->get_user();
        $this->textformat = CF::get(TextComponent::class);
    }

    private function _map_dates(array &$input): void
    {
        $date = $input["date_from"] ?? "";
        $date = $this->datecomp->set_date1($date)->explode(DateComponent::SOURCE_YMD)->to_db()->get();
        $input["date_from"] = $date;
        $date = $input["date_to"] ?? "";
        $date = $this->datecomp->set_date1($date)->explode(DateComponent::SOURCE_YMD)->to_db()->get();
        $input["date_to"] = $date;
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::XXXS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
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
        if (!$insert = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entityxxx->map_request($insert);
        $insert["uuid"] = uniqid();
        $insert["slug"] = $this->textformat->set_text($insert["description"])->slug();
        if (!$this->auth->is_system()) $insert["id_owner"] = $this->auth->get_idowner();

        $this->entityxxx->add_sysinsert($insert, $this->authuser["id"]);
        $id = $this->repoxxx->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}