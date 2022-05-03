<?php
namespace App\Restrict\UserPermissions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Restrict\Users\Domain\Enums\UserPermissionType;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;


final class PromotionCapSubscriptionEventHandler extends AppService implements IEventSubscriber
{

    public function __construct()
    {

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