<?php
namespace App\Open\PromotionCaps\Application;

use App\Checker\Application\CheckerService;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapUserType;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionWasExecutedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersEntity;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserWasCreatedEvent;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Enums\ExceptionType;
use App\Picklist\Domain\Enums\AppArrayType;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapsConfirmService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $promotion;
    private array $promotionui;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $promotionuuid = $this->input["promotionuuid"];
        $this->promotion = $this->repopromotion->get_by_uuid($promotionuuid, [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "id_owner"
        ]);

        SF::get(
            PromotionCapCheckService::class,
            [
                "email" => ($this->input["email"] ?? ""),
                "promotion" => $this->promotion,
            ]
        )
        ->is_suitable_or_fail();
    }

    public function __invoke(): array
    {
        $this->_load_request();
        $this->_load_promotion();

        $entitypromouser = MF::get(PromotionCapUsersEntity::class);
        $promocapuser = $entitypromouser->map_request($promocapuser);
        $this->_map_entity($promocapuser);
        $id = $this->repopromocapuser->insert($promocapuser);

        $promocapuser["remote_ip"] = $this->request->get_remote_ip();
        $promocapuser["date_subscription"] = date("Y-m-d H:i:s");

        EventBus::instance()->publish(...[
            PromotionCapUserWasCreatedEvent::from_primitives($id, $promocapuser),
            PromotionCapActionWasExecutedEvent::from_primitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $id,
                "id_type" => PromotionCapActionType::SUBSCRIBED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip()
            ])
        ]);

        return [
            "description" => __("You have successfully subscribed. Please check your email to confirm your subscription!")
        ];
    }
}