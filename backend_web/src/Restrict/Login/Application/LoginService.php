<?php
namespace App\Restrict\Login\Application;

use App\Restrict\Login\Application\Dtos\LoginDto;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\SessionTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\SessionFactory as SsF;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Restrict\Users\Domain\UserPermissionsRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Domain\Enums\SessionType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Domain\Enums\TimezoneType;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Domain\Enums\ExceptionType;

final class LoginService extends AppService
{
    use SessionTrait;

    private string $domain;
    private ComponentEncdecrypt $componentEncdecrypt;
    private UserRepository $userRepository;
    private UserPermissionsRepository $userPermissionsRepository;
    private UserPreferencesRepository $userPreferencesRepository;

    private LoginDto $loginDto;

    public function __construct()
    {
        $this->componentEncdecrypt = $this->_get_encdec();
        $this->userRepository = RF::get(UserRepository::class);
        $this->userPermissionsRepository = RF::get(UserPermissionsRepository::class);
        $this->userPreferencesRepository = RF::get(UserPreferencesRepository::class);
    }

    public function get_access_or_fail(LoginDto $loginDto): array
    {
        $this->loginDto = $loginDto;
        if (!$email =$this->loginDto->email())
            $this->_exception(__("Empty email"), ExceptionType::CODE_BAD_REQUEST);

        if (!$password = $this->loginDto->password())
            $this->_exception(__("Empty password"), ExceptionType::CODE_BAD_REQUEST);

        $aruser = $this->userRepository->get_by_email($email);
        if (!($secret = ($aruser["secret"] ?? ""))) $this->_exception(__("Invalid data"), ExceptionType::CODE_BAD_REQUEST);

        if (!$this->componentEncdecrypt->check_hashpassword($password, $secret))
            $this->_exception(__("Unauthorized"), ExceptionType::CODE_UNAUTHORIZED);

        $aruser[SessionType::AUTH_USER_PERMISSIONS] = json_decode(
            $this->userPermissionsRepository->get_by_user($iduser = (int)$aruser["id"])["json_rw"] ?? "",
            true,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $tz = $this->userPreferencesRepository->get_value_by_user_and_key($iduser, UserPreferenceType::KEY_TZ);
        if (!$tz) $tz = TimezoneType::UTC;
        $aruser[SessionType::AUTH_USER_TZ] = $tz;
        $aruser[SessionType::AUTH_USER_ID_TZ] = RF::get(ArrayRepository::class)->get_timezone_id_by_description($tz);

        $session = SsF::get();
        $session
            ->add(SessionType::AUTH_USER, $aruser)
            ->add(SessionType::AUTH_USER_LANG, $lang = ($aruser["e_language"] ?? "en"))
        ;

        $defaultUrl = $this->userPreferencesRepository->get_value_by_user_and_key($iduser, UserPreferenceType::URL_DEFAULT_MODULE);
        if (!$defaultUrl)
            $defaultUrl = UrlType::RESTRICT;

        return [
            "lang" => $lang,
            UserPreferenceType::URL_DEFAULT_MODULE => $defaultUrl
        ];
    }

}