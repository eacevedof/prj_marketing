<?php
namespace App\Restrict\Promotions\Application;

use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

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
        //comprobar que la promocion sea del tipo raffle
        //comprobar que la fecha de raffle sea la correcta

        $promocapuserrepo = RF::get(PromotionCapUsersRepository::class);
        $r = [
            "winners" => $promocapuserrepo->get_raffle_winners($this->idpromotion, ["m.id"]),
            "participants" => $promocapuserrepo->get_raffle_participants($this->idpromotion, ["m.id"]),
        ];

        if ($r["winners"])
            $this->_exception(__("Raffle already done"), ExceptionType::CODE_BAD_REQUEST);

        if (!$r["participants"])
            $this->_exception(__("No participants for this raffle"), ExceptionType::CODE_BAD_REQUEST);

        $winners = $this->_get_winners($r["participants"]);

        $promocapuserrepo = RF::get(PromotionCapSubscriptionsRepository::class);
        foreach ($winners as $ipos => $id)
            $promocapuserrepo->update_raffle_winner($id, $ipos);
    }

    private function _get_winners(array $participants): array
    {
        $participants = array_column($participants, "id");

        $winners = [];
        $tmp = array_rand($participants);
        $winners[1] = $participants[$tmp[0]];
        unset($participants[$tmp[0]]);

        if (!$participants) return $winners;

        $tmp = array_rand($participants);
        $winners[2] = $participants[$tmp[0]];
        unset($participants[$tmp[0]]);

        if (!$participants) return $winners;

        $tmp = array_rand($participants);
        $winners[3] = $participants[$tmp[0]];
        unset($participants[$tmp[0]]);
        return $winners;
    }

    public function __invoke(): array
    {
        $this->_check_entity_permission();
        return $this->_update();
    }
}