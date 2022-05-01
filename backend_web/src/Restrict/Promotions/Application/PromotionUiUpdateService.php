<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Restrict\Promotions\Domain\PromotionEntity;
use App\Restrict\Promotions\Domain\PromotionUiEntity;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionUiUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;
    private FieldsValidator $validator;
    private PromotionUiEntity $entitypromotionui;
    private int $idpromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$promouuid = $this->input["_promotionuuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repopromotion = RF::get(PromotionRepository::class);
        if (!$this->idpromotion = $this->repopromotion->get_id_by_uuid($promouuid))
            $this->_exception(__("{0} with code {1} not found", __("Promotion"), $promouuid));
   
        $this->entitypromotionui = MF::get(PromotionUiEntity::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class)->set_model($this->entitypromotionui);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::USER_PERMISSIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->auth->is_root_super()) return;

        //un root puede cambiar la entidad de cualquiera
        if ($this->auth->is_root()) return;

        //un sysadmin puede cambiar los de cualquiera
        if ($this->auth->is_sysadmin()) return;

        $identowner = $this->repopromotion->get_by_id($this->idpromotion)["id_owner"];
        //si es bow o bm y su idwoner es el de la ui
        if ($this->auth->get_idowner() === $identowner)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("uuid", "uuid", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_owner", "id_owner", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("json_rw", "json_rw", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
        ;
        
        return $this->validator;
    }

    private function _update(array $update, array $promotionui): array
    {
        if ($promotionui["id"] !== $update["id"])
            $this->_exception(
                __("This permission does not belong to user {0}", $this->input["_promouuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );

        //no se hace skip pq se tiene que cumplir todo
        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitypromotionui->map_request($update);
        $this->_check_entity_permission();
        $this->entitypromotionui->add_sysupdate($update, $this->authuser["id"]);
        $this->repopromotionui->update($update);
        return [
            "id" => $promotionui["id"],
            "uuid" => $update["uuid"]
        ];
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission();
        $this->validator = VF::get($update, $this->entitypromotionui);

        return ($promotionui = $this->repopromotionui->get_by_id($this->idpromotion))
            ? $this->_update($update, $promotionui)
            : [];
    }
}