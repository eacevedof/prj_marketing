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

final class PromotionRaffleUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
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

        if ($this->repopromotion->has_subscribers_by_uuid($promouuid))
            $this->_exception(__("{0} with code {1} is not editable", __("Promotion"), $promouuid));

        $this->idpromotion = $promotion["id"];
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

        $identowner = (int) $this->repopromotion->get_by_id($this->idpromotion)["id_owner"];
        //si es bow o bm y su idwoner es el del sorteo
        if ($this->auth->get_idowner() === $identowner)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _update(): array
    {
        dd($this->input);

    }

    public function __invoke(): array
    {
        $this->_check_entity_permission();
        return $this->_update();
    }
}