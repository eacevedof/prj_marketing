<?php

namespace App\Restrict\UserPermissions\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Restrict\Users\Domain\{UserPermissionsEntity, UserPermissionsRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserPermissionsInsertEventHandler extends AppService implements IEventSubscriber
{
    use RequestTrait;

    private AuthService $authService;
    private UserPermissionsEntity $userpermission;
    private UserPermissionsRepository $repouserpermissions;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->userpermission = MF::getInstanceOf(UserPermissionsEntity::class);
        $this->repouserpermissions = RF::getInstanceOf(UserPermissionsRepository::class);
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== UserWasCreatedEvent::class) {
            return $this;
        }

        $permission = [
            "id_user" => $domainEvent->aggregateId(),
            "uuid" => uniqid(),
            "json_rw" => "[]",
        ];

        $this->userpermission->addSysInsert($permission, $this->authService->getAuthUserArray()["id"]);
        $this->repouserpermissions->insert($permission);
        return $this;
    }
}
