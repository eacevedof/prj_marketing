<?php
namespace App\Restrict\UserPreferences\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserPreferencesEntity;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Restrict\Users\Domain\Events\UserWasCreated;
use App\Shared\Domain\Enums\UrlType;

final class UserPreferencesInsertService extends AppService implements IEventSubscriber
{
    private AuthService $auth;
    private UserPreferencesEntity $userppref;
    private UserPreferencesRepository $repouserprefs;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->userppref = MF::get(UserPreferencesEntity::class);
        $this->repouserprefs = RF::get(UserPreferencesRepository::class);
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==UserWasCreated::class) return $this;

        $prefs = [
            "id_user" => $domevent->aggregate_id(),
            "pref_key" => UserPreferenceType::URL_DEFAULT_MODULE,
            "pref_value" => UrlType::RESTRICT
        ];

        $this->userppref->add_sysinsert($prefs, $this->auth->get_user()["id"]);
        $this->repouserprefs->insert($prefs);
        return $this;
    }
}