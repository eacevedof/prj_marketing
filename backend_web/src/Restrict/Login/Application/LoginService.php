<?php
namespace App\Restrict\Login\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\SessionTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Restrict\Users\Domain\UserPermissionsRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Shared\Domain\Enums\SessionType;
use App\Shared\Domain\Enums\UrlType;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Domain\Enums\ExceptionType;

final class LoginService extends AppService
{
    use SessionTrait;

    private string $domain;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_load_session();
        $this->encdec = $this->_get_encdec();
        $this->repouser = RF::get(UserRepository::class);
        $this->repopermission = RF::get(UserPermissionsRepository::class);
        $this->repouserprefs = RF::get(UserPreferencesRepository::class);
    }

    public function get_access(): array
    {
        $email = $this->input["email"];
        if (!$email) $this->_exception(__("Empty email"), ExceptionType::CODE_BAD_REQUEST);

        $password = $this->input["password"];
        if (!$password) $this->_exception(__("Empty password"), ExceptionType::CODE_BAD_REQUEST);

        $aruser = $this->repouser->get_by_email($email);
        if (!($secret = ($aruser["secret"] ?? ""))) $this->_exception(__("Invalid data"), ExceptionType::CODE_BAD_REQUEST);

        if (!$this->encdec->check_hashpassword($password, $secret))
            $this->_exception(__("Unauthorized"), ExceptionType::CODE_UNAUTHORIZED);

        $aruser[SessionType::AUTH_USER_PERMISSIONS] = json_decode(
            $this->repopermission->get_by_user($iduser = (int)$aruser["id"])["json_rw"] ?? "", 1
        );

        $tz = $this->repouserprefs->get_value_by_user_and_key($iduser, UserPreferenceType::KEY_TZ);
        if (!$tz) $tz = UserPreferenceType::DEFAULT_TZ;
        $aruser[SessionType::AUTH_USER_TZ] = $tz;
        $aruser[SessionType::AUTH_USER_ID_TZ] = RF::get(ArrayRepository::class)->get_timezone_id_by_description($tz);

        $this->session
            ->add(SessionType::AUTH_USER, $aruser)
            ->add(SessionType::AUTH_USER_LANG, $lang = ($aruser["e_language"] ?? "en"))
        ;

        $defurl = $this->repouserprefs->get_value_by_user_and_key($iduser, UserPreferenceType::URL_DEFAULT_MODULE);
        if (!$defurl) $defurl = UrlType::RESTRICT;

        return [
            "lang" => $lang,
            UserPreferenceType::URL_DEFAULT_MODULE => $defurl
        ];
    }
}