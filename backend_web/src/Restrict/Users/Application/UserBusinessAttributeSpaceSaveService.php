<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeEntity;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UserBusinessAttributeSpaceSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private int $baiduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if (!$useruuid = $input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get(UserRepository::class);
        if (!$this->baiduser = $this->repouser->get_id_by_uuid($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));
        
        $this->authuser = $this->auth->get_user();
        $this->_load_input($input);
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _load_input($input): void
    {
        $input = $this->_get_req_without_ops($input);
        $this->input = [
            //"space_about_title" => $input["space_about_title"] ?? "",
            "space_about" => $input["space_about"] ?? "",
            //"space_plan_title" => $input["space_plan_title"] ?? "",
            "space_plan" => $input["space_plan"] ?? "",
            //"space_location_title" => $input["space_location_title"] ?? "",
            "space_location" => $input["space_location"] ?? "",
            //"space_contact_title" => $input["space_contact_title"] ?? "",
            "space_contact" => $input["space_contact"] ?? "",
        ];
    }

    private function _check_entity_permission(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->auth->is_system()) return;

        //si el us en sesion quiere cambiar su bd
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->baiduser)
            return;

        //si el propietario del us de sesion coincide con el de la entidad
        if ($this->auth->get_idowner() === $this->baiduser)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _validate(): array
    {
        $validator = VF::get($this->input);
        $validator
            ->add_rule("space_about", "space_about", function ($data) {
                if (!$data["value"]) return __("Empty field is not allowed");
            })
            ->add_rule("space_plan", "space_plan", function ($data) {
                if (!$data["value"]) return __("Empty field is not allowed");
            })
            ->add_rule("space_location", "space_location", function ($data) {
                if (!$data["value"]) return __("Empty field is not allowed");
            })
            ->add_rule("space_contact", "space_contact", function ($data) {
                if (!$data["value"]) return __("Empty field is not allowed");
            });

        return $validator->get_errors();
    }

    private function _update(): array
    {
        if ($errors = $this->_validate()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $this->_check_entity_permission();

        $tentitybusinessattrib = MF::get(BusinessAttributeEntity::class);
        $repobusinessattrib = RF::get(BusinessAttributeRepository::class)->set_model($tentitybusinessattrib);
        $businessattribs = $repobusinessattrib->get_spacepage_by_iduser($this->baiduser);
        $businessattribs = array_map(function (array $item){
            $keys = array_keys($this->input);
            if (in_array($key = $item["attr_key"], $keys)) {
                $item["attr_value"] = $this->input[$key];
                return $item;
            }
            return null;
        }, $businessattribs);
        $businessattribs = array_filter($businessattribs);

        foreach ($businessattribs as $attrib) {
            $update = $tentitybusinessattrib->map_request($attrib);
            $tentitybusinessattrib->add_sysupdate($update, $this->authuser["id"]);
            $repobusinessattrib->update($update);
        }

        return $businessattribs;
    }

    public function __invoke(): array
    {
        $this->_check_entity_permission();
        return $this->_update();
    }
}