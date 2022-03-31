<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\PromotionUiEntity;
use App\Restrict\Users\Domain\PromotionUiRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class PromotionUiDeleteService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private PromotionUiRepository $repouserprefs;
    private FieldsValidator $validator;
    private PromotionUiEntity $entityuserprefs;
    private int $iduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get(UserRepository::class);
        if (!$this->iduser = $this->repouser->get_id_by_uuid($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));

        $this->entityuserprefs = MF::get(PromotionUiEntity::class);
        $this->repouserprefs = RF::get(PromotionUiRepository::class)->set_model($this->entityuserprefs);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::USER_PREFERENCES_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(int $id): void
    {
        if(!$id = $this->repouserprefs->get_by_id_and_user($id, $this->iduser))
            $this->_exception(
                __("{0} {1} does not exist", __("Preference"), $id),
                ExceptionType::CODE_BAD_REQUEST
            );

        if ($this->auth->is_root_super() || $this->auth->is_root()) return;

        $prefuser = $this->repouser->get_by_id($this->iduser);
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->iduser) return;
        
        if ($this->auth->is_sysadmin() && $this->auth->is_business($prefuser["id_profile"])) return;

        $identowner = $this->repouser->get_idowner($this->iduser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && $this->auth->is_business_manager($prefuser["id_profile"])
            && ((int) $this->authuser["id"]) === $identowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _delete(array $prefreq): array
    {
        $prefreq = $this->entityuserprefs->map_request($prefreq);
        $this->_check_entity_permission((int) $prefreq["id"]);
        $updatedate = $this->repouserprefs->get_sysupdate($prefreq);
        $this->entityuserprefs->add_sysdelete($prefreq, $updatedate, $this->authuser["id"]);
        $this->repouserprefs->update($prefreq);
        $result = $this->repouserprefs->get_by_user($this->iduser);

        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }

    public function __invoke(): array
    {
        if (!$prefreq = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        return $this->_delete($prefreq);
    }
}