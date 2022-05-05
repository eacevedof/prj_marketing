<?php
namespace App\Restrict\UserPermissions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserPermissionsEntity;
use App\Restrict\Users\Domain\UserPermissionsRepository;
use App\Restrict\Users\Domain\Enums\UserPermissionType;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class UserPermissionsInsertEventHandler extends AppService implements IEventSubscriber
{
    use RequestTrait;

    private AuthService $auth;
    private UserPermissionsEntity $userpermission;
    private UserPermissionsRepository $repouserpermissions;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->userpermission = MF::get(UserPermissionsEntity::class);
        $this->repouserpermissions = RF::get(UserPermissionsRepository::class);
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==UserWasCreatedEvent::class) return $this;

        $permission = [
            "id_user" => $domevent->aggregate_id(),
            "uuid" => uniqid(),
            "json_rw" => "[]",
        ];

        $this->userpermission->add_sysinsert($permission, $this->auth->get_user()["id"]);
        $this->repouserpermissions->insert($permission);
        return $this;
    }
}