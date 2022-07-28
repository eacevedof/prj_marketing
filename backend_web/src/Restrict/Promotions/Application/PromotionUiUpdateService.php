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
        if (!$promotion = $this->repopromotion->get_by_uuid($promouuid))
            $this->_exception(__("{0} with code {1} not found", __("Promotion"), $promouuid));

        if ($promotion["is_launched"])
            $this->_exception(__("{0} with code {1} is not editable", __("Promotion"), $promouuid));

        $this->idpromotion = $promotion["id"];
        $this->entitypromotionui = MF::get(PromotionUiEntity::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class)->set_model($this->entitypromotionui);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_UI_WRITE))
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
        $fn_isvalidbool = function (string $value){
            return in_array($value, ["0", "1"]);
        };

        $fn_validint = function (string $value){
            $value = (int) $value;
            return ($value > -1 && $value < 1000);
        };

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
            ->add_rule("input_email", "input_email", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_email", "pos_email", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_gender", "input_gender", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_gender", "pos_gender", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_language", "input_language", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_language", "pos_language", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_name1", "input_name1", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_name1", "pos_name1", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_name2", "input_name2", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_name2", "pos_name2", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_phone1", "input_phone1", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_phone1", "pos_phone1", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_address", "input_address", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_address", "pos_address", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_birthdate", "input_birthdate", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_birthdate", "pos_birthdate", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_country", "input_country", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_country", "pos_country", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_is_mailing", "input_is_mailing", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_is_mailing", "pos_is_mailing", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->add_rule("input_is_terms", "input_is_terms", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->add_rule("pos_is_terms", "pos_is_terms", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
        ;
        
        return $this->validator;
    }

    private function _remove_readonly(array &$promotionui): void
    {
        $remove = [
            "uuid", "id_owner", "id_promotion"
        ];
        foreach ($remove as $field)
            unset($promotionui[$field]);
    }

    private function _update(array $promouireq, array $promotionui): array
    {
        if ($promouireq["id"] !== $promotionui["id"])
            $this->_exception(
                __("This promotion UI does not belong to promotion {0}", $this->input["_promotionuuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );

        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $promouireq = $this->entitypromotionui->map_request($promouireq);
        $this->_check_entity_permission();
        $this->entitypromotionui->add_sysupdate($promouireq, $this->authuser["id"]);
        $this->_remove_readonly($promouireq);
        $this->repopromotionui->update($promouireq);
        return [
            "id" => $promotionui["id"],
            "uuid" => $promotionui["uuid"]
        ];
    }

    public function __invoke(): array
    {
        if (!$promouireq = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission();
        $this->validator = VF::get($promouireq, $this->entitypromotionui);

        $promotionui = $this->repopromotionui->get_by_promotion($this->idpromotion);
        if (!$promotionui)
            $this->_exception(__("{0} not found!", __("Promotion UI")), ExceptionType::CODE_NOT_FOUND);

        return $this->_update($promouireq, $promotionui);
    }
}