<?php

namespace App\Restrict\BusinessAttributes\Application;

use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Restrict\BusinessAttributes\Domain\{BusinessAttributeEntity, BusinessAttributeRepository};
use App\Shared\Infrastructure\Factories\{
    EntityFactory as MF,
    RepositoryFactory as RF,
    ServiceFactory as SF
};

final class BusinessAttributesInsertEventHandler extends AppService implements IEventSubscriber
{
    use RequestTrait;

    private AuthService $authService;
    private BusinessAttributeEntity $businessattribute;
    private BusinessAttributeRepository $repobusinessattributes;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->businessattribute = MF::getInstanceOf(BusinessAttributeEntity::class);
        $this->repobusinessattributes = RF::getInstanceOf(BusinessAttributeRepository::class);
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== UserWasCreatedEvent::class) {
            return $this;
        }

        if (!RF::getInstanceOf(UserRepository::class)->isIdUserBusinessOwner($idUser = $domainEvent->aggregateId())) {
            return $this;
        }

        $attributes = [
            [
                "id_user" => $idUser,
                "attr_key" => "space_about_title",
                "attr_value" => "About",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_about",
                "attr_value" => "",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_plan_title",
                "attr_value" => "Points program",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_plan",
                "attr_value" => "",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_location_title",
                "attr_value" => "Where are we?",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_location",
                "attr_value" => "",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_contact_title",
                "attr_value" => "Contact",
            ],
            [
                "id_user" => $idUser,
                "attr_key" => "space_contact",
                "attr_value" => "",
            ],
        ];

        foreach ($attributes as $attribute) {
            $this->businessattribute->addSysInsert($attribute, $this->authService->getAuthUserArray()["id"]);
            $this->repobusinessattributes->insert($attribute);
        }
        return $this;
    }
}
