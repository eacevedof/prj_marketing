<?php
namespace App\Restrict\BusinessAttributes\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeEntity;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class BusinessAttributesInsertEventHandler extends AppService implements IEventSubscriber
{
    use RequestTrait;

    private AuthService $auth;
    private BusinessAttributeEntity $businessattribute;
    private BusinessAttributeRepository $repobusinessattributes;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->businessattribute = MF::get(BusinessAttributeEntity::class);
        $this->repobusinessattributes = RF::get(BusinessAttributeRepository::class);
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==UserWasCreatedEvent::class) return $this;

        if (!RF::get(UserRepository::class)->is_business($iduser = $domevent->aggregate_id()))
            return $this;

        $attributes = [
            [
                "id_user" => $iduser,
                "attr_key" => "space_about_title",
                "attr_value" => "About",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_about",
                "attr_value" => "",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_plan_title",
                "attr_value" => "Points program",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_plan",
                "attr_value" => "",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_location_title",
                "attr_value" => "Where are we?",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_location",
                "attr_value" => "",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_contact_title",
                "attr_value" => "Contact",
            ],
            [
                "id_user" => $iduser,
                "attr_key" => "space_contact",
                "attr_value" => "",
            ],
        ];

        foreach ($attributes as $attribute) {
            $this->businessattribute->add_sysinsert($attribute, $this->auth->get_user()["id"]);
            $this->repobusinessattributes->insert($attribute);
        }
        return $this;
    }
}