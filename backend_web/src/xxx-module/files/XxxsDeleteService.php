<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Services\Auth\AuthService;
use App\Factories\EntityFactory as MF;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Models\Base\XxxEntity;
use App\Repositories\Base\XxxRepository;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;

final class XxxsDeleteService extends AppService
{
    private AuthService $auth;
    private array $authxxx;
    private XxxRepository $repoxxx;
    private XxxEntity $entityxxx;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->authxxx = $this->auth->get_user();
        $this->entityxxx = MF::get("Base/Xxx");
        $this->repoxxx = RF::get("Base/XxxRepository")->set_model($this->entityxxx);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::XXXS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        $idxxx = (int)$entity["id"];
        $idauthxxx = (int)$this->authxxx["id"];

        //si el logado quiere borrarse a si mismo
        if ($idauthxxx === $idxxx)
            $this->_exception(
                __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
            );

        if ($this->auth->is_root()) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identyowner = $this->repoxxx->get_ownerid($idxxx);
        //si el usuario logado es owner y quiere eliminar un manager que le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $idauthxxx === $identyowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _check_entity_undelete_permission(array $entity): void
    {
        $idxxx = (int) $entity["id"];
        $idauthxxx = (int) $this->authxxx["id"];
        if ($idauthxxx === $idxxx)
            $this->_exception(
                __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
            );

        if ($this->auth->is_root()) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $entity = $this->input;
        if (!$idxxx = $this->repoxxx->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idxxx;
        if (!$this->entityxxx->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if ($this->repoxxx->is_deleted($idxxx))
            $this->_exception(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repoxxx->get_by_id($idxxx);
        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repoxxx->get_sysupdate($entity);
        $this->entityxxx->add_sysdelete($entity, $updatedate, $this->authxxx["id"]);
        $affected = $this->repoxxx->update($entity);
        //$this->repoxxx->delete($entity);
        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
    
    public function undelete(): array
    {
        $entity = $this->input;
        if (!$idxxx = $this->repoxxx->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idxxx;
        if (!$this->entityxxx->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if (!$this->repoxxx->is_deleted($idxxx))
            $this->_exception(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repoxxx->get_by_id($idxxx);
        $this->_check_entity_undelete_permission($entity);
        $idauthxxx = $this->authxxx["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $idxxx,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repoxxx->get_csvcru($entity, $idauthxxx),
        ];

        $this->entityxxx->add_sysupdate($entity, $idauthxxx);
        $affected = $this->repoxxx->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];                
    }
}