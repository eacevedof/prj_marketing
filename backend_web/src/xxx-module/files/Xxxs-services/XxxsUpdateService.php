<?php
namespace App\Restrict\Xxxs\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Xxxs\Domain\XxxEntity;
use App\Restrict\Xxxs\Domain\XxxRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class XxxsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;
    private XxxRepository $repoxxx;
    private FieldsValidator $validator;
    private XxxEntity $entityxxx;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_throwException(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entityxxx = MF::getInstanceOf(XxxEntity::class);
        $this->validator = VF::getFieldValidator($this->input, $this->entityxxx);
        $this->repoxxx = RF::getInstanceOf(XxxRepository::class);
        $this->repoxxx->setAppEntity($this->entityxxx);
        $this->authUserArray= $this->authService->getAuthUserArray();
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

    private function _checkEntityPermissionOrFail(array $entity): void
    {
        $idxxx = $this->repoxxx->getEntityIdByEntityUuid($entity["uuid"]);
        $idauthuser = (int)$this->authUserArray["id"];
        if ($this->authService->isAuthUserRoot() || $idauthuser === $idxxx) return;

        if ($this->authService->isAuthUserSysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repoxxx->getIdOwnerByIdUser($idxxx);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->authService->isAuthUserBusinessOwner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identowner
        )
            return;

        $this->_throwException(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
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
        if (!$update = $this->_getRequestWithoutOperations($this->input))
            $this->_throwException(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_skip_validation()->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entityxxx->getAllKeyValueFromRequest($update);
        $this->_checkEntityPermissionOrFail($update);
        $this->entityxxx->addSysUpdate($update, $this->authUserArray["id"]);

        $affected = $this->repoxxx->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}