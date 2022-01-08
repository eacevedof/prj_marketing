<?php
namespace App\Services\Restrict;

use App\Enums\ExceptionType;
use App\Enums\PreferenceType;
use App\Services\AppService;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;
use App\Repositories\Base\UserPermissionsRepository;

use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Enums\SessionType;

final class LoginService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_load_session();
        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
        $this->repoprefs = RF::get("Base/UserPreferences");
    }

    public function in(): array
    {
        $email = $this->input["email"];
        if (!$email) $this->_exception(__("Empty email"), ExceptionType::CODE_BAD_REQUEST);

        $password = $this->input["password"];
        if (!$password) $this->_exception(__("Empty password"), ExceptionType::CODE_BAD_REQUEST);

        $aruser = $this->repository->get_by_email($email);
        if (!($secret = ($aruser["secret"] ?? ""))) $this->_exception(__("Invalid data"), ExceptionType::CODE_BAD_REQUEST);

        if (!$this->encdec->check_hashpassword($password, $secret))
            $this->_exception(__("Unauthorized"), ExceptionType::CODE_UNAUTHORIZED);

        $aruser[SessionType::AUTH_USER_PERMISSIONS] = $this->permissionrepo->get_by_user($iduser = $aruser["id"]);

        $this->session
            ->add(SessionType::AUTH_USER, $aruser)
            ->add(SessionType::LANG, $lang = ($aruser["e_language"] ?? "en"))
        ;

        $prefs = $this->repoprefs->get_by_user($iduser, $prefkey = PreferenceType::URL_DEFAULT_MODULE);
        $prefs = $prefs[0]["pref_value"] ?? "/restrict";


        return [
            "lang" => $lang,
            $prefkey => $prefs
        ];
    }
}