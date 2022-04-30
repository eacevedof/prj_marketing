<?php
namespace App\Restrict\UserPreferences\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserPreferencesEntity;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Restrict\Users\Domain\Events\UserWasCreated;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class UserPreferencesInsertService extends AppService implements IEventSubscriber
{
    use RequestTrait;

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

        $this->_load_request();
        $tz = CF::get(UtcComponent::class)->get_timezone_by_ip($this->request->get_remote_ip());
        $prefs = [
            "id_user" => $domevent->aggregate_id(),
            "pref_key" => UserPreferenceType::KEY_TZ,
            "pref_value" => $tz
        ];
        $this->repouserprefs->insert($prefs);

        return $this;
    }
}