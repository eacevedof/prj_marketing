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

    private AuthService $authService;
    private array $authUserArray;
    private XxxRepository $repoxxx;
    private FieldsValidator $validator;
    private XxxEntity $entityxxx;
    private TextComponent $textformat;
    private DateComponent $datecomp;
    private ArrayRepository $repoapparray;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->datecomp = CF::getInstanceOf(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $input;
        $this->entityxxx = MF::getInstanceOf(XxxEntity::class);
        $this->validator = VF::getFieldValidator($this->input, $this->entityxxx);

        $this->repoxxx = RF::getInstanceOf(XxxRepository::class);
        $this->repoapparray = RF::getInstanceOf(ArrayRepository::class);
        $this->authUserArray= $this->authService->getAuthUserArray();
        $this->textformat = CF::getInstanceOf(TextComponent::class);
    }

    private function _map_dates(array &$input): void
    {
        $date = $input["date_from"] ?? "";
        $date = $this->datecomp->getDateInDbFormat($date);
        $input["date_from"] = $date;
        $date = $input["date_to"] ?? "";
        $date = $this->datecomp->getDateInDbFormat($date);
        $input["date_to"] = $date;
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) return;

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_WRITE))
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _skip_validation(): self
    {
        $this->validator->addSkipableField("id");
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
        if (!$insert = $this->_getRequestWithoutOperations($this->input))
            $this->_throwException(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $insert = $this->entityxxx->getAllKeyValueFromRequest($insert);
        $insert["uuid"] = uniqid();
        $insert["slug"] = $this->textformat->getSlug($insert["description"]);
        if (!$this->authService->hasAuthUserSystemProfile()) $insert["id_owner"] = $this->authService->getIdOwner();

        $this->entityxxx->addSysInsert($insert, $this->authUserArray["id"]);
        $id = $this->repoxxx->insert($insert);

        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}