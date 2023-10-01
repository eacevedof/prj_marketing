<?php

namespace App\Restrict\UserPreferences\Application;

use App\Shared\Domain\Enums\UrlType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Restrict\Users\Domain\{UserPreferencesEntity, UserPreferencesRepository};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserPreferencesInsertEventHandler extends AppService implements IEventSubscriber
{
    use RequestTrait;

    private AuthService $authService;
    private UserPreferencesEntity $userppref;
    private UserPreferencesRepository $repouserprefs;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->userppref = MF::getInstanceOf(UserPreferencesEntity::class);
        $this->repouserprefs = RF::getInstanceOf(UserPreferencesRepository::class);
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== UserWasCreatedEvent::class) {
            return $this;
        }

        $prefs = [
            "id_user" => $domainEvent->aggregateId(),
            "pref_key" => UserPreferenceType::URL_DEFAULT_MODULE,
            "pref_value" => UrlType::RESTRICT
        ];

        $this->userppref->addSysInsert($prefs, $this->authService->getAuthUserArray()["id"]);
        $this->repouserprefs->insert($prefs);

        $this->_loadRequestComponentInstance();
        $tz = CF::getInstanceOf(UtcComponent::class)->getTimezoneByIp($this->requestComponent->getRemoteIp());
        $prefs = [
            "id_user" => $domainEvent->aggregateId(),
            "pref_key" => UserPreferenceType::KEY_TZ,
            "pref_value" => $tz
        ];
        $this->userppref->addSysInsert($prefs, $this->authService->getAuthUserArray()["id"]);
        $this->repouserprefs->insert($prefs);

        return $this;
    }
}
