<?php

namespace App\Restrict\PromotionsUi\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Restrict\Promotions\Domain\Events\PromotionWasCreatedEvent;
use App\Restrict\Promotions\Domain\{PromotionUiEntity, PromotionUiRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionUiInsertService extends AppService implements IEventSubscriber
{
    private AuthService $authService;
    private PromotionUiEntity $promotionUiEntity;
    private PromotionUiRepository $promotionUiRepository;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->promotionUiEntity = MF::getInstanceOf(PromotionUiEntity::class);
        $this->promotionUiRepository = RF::getInstanceOf(PromotionUiRepository::class);
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== PromotionWasCreatedEvent::class) {
            return $this;
        }

        $promotionUi = [
            "uuid" => uniqid(),
            "id_owner" => $domainEvent->idOwner(),
            "id_promotion" => $domainEvent->aggregateId(),
            "input_email" => 1,
            "pos_email" => 10,
            "input_name1" => 1,
            "pos_name1" => 20,
            "input_phone1" => 0,
            "pos_phone1" => 30,
            "input_name2" => 0,
            "pos_name2" => 40,
            "input_language" => 0,
            "pos_language" => 50,
            "input_country" => 0,
            "pos_country" => 60,
            "input_birthdate" => 0,
            "pos_birthdate" => 70,
            "input_gender" => 0,
            "pos_gender" => 80,
            "input_address" => 0,
            "pos_address" => 90,
            "input_is_mailing" => 0,
            "pos_is_mailing" => 100,
            "input_is_terms" => 0,
            "pos_is_terms" => 110,
        ];

        $this->promotionUiEntity->addSysInsert($promotionUi, $this->authService->getAuthUserArray()["id"]);
        $this->promotionUiRepository->insert($promotionUi);
        return $this;
    }
}
