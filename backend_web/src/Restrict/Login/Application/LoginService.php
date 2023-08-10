<?php

namespace App\Restrict\Login\Application;

use App\Restrict\Login\Application\Dtos\LoginDto;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\SessionTrait;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use TheFramework\Components\Session\ComponentEncDecrypt;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\SessionFactory as SsF;
use App\Shared\Domain\Enums\{ExceptionType, SessionType, TimezoneType, UrlType};
use App\Restrict\Users\Domain\{UserPermissionsRepository, UserPreferencesRepository, UserRepository};

final class LoginService extends AppService
{
    use SessionTrait;

    private string $domain;
    private ComponentEncDecrypt $componentEncdecrypt;
    private UserRepository $userRepository;
    private UserPermissionsRepository $userPermissionsRepository;
    private UserPreferencesRepository $userPreferencesRepository;

    private LoginDto $loginDto;

    public function __construct()
    {
        $this->componentEncdecrypt = $this->_getEncDecryptInstance();
        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        $this->userPermissionsRepository = RF::getInstanceOf(UserPermissionsRepository::class);
        $this->userPreferencesRepository = RF::getInstanceOf(UserPreferencesRepository::class);
    }

    public function get_access_or_fail(LoginDto $loginDto): array
    {
        $this->loginDto = $loginDto;
        if (!$email = $this->loginDto->email()) {
            $this->_throwException(__("Empty email"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$password = $this->loginDto->password()) {
            $this->_throwException(__("Empty password"), ExceptionType::CODE_BAD_REQUEST);
        }

        $aruser = $this->userRepository->getUserByEmail($email);
        if (!($secret = ($aruser["secret"] ?? ""))) {
            $this->_throwException(__("Invalid data"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$this->componentEncdecrypt->isValidPassword($password, $secret)) {
            $this->_throwException(__("Unauthorized"), ExceptionType::CODE_UNAUTHORIZED);
        }

        $aruser[SessionType::AUTH_USER_PERMISSIONS] = json_decode(
            $this->userPermissionsRepository->getUserPermissionByIdUser($idUser = (int) $aruser["id"])["json_rw"] ?? "",
            true,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $tz = $this->userPreferencesRepository->getPrefValueByIdUserAndPrefKey($idUser, UserPreferenceType::KEY_TZ);
        if (!$tz) {
            $tz = TimezoneType::UTC;
        }
        $aruser[SessionType::AUTH_USER_TZ] = $tz;
        $aruser[SessionType::AUTH_USER_ID_TZ] = RF::getInstanceOf(ArrayRepository::class)->getTimezoneIdByDescription($tz);

        $session = SsF::get();
        $session
            ->addValue(SessionType::AUTH_USER, $aruser)
            ->addValue(SessionType::AUTH_USER_LANG, $lang = ($aruser["e_language"] ?? "en"))
        ;

        $defaultUrl = $this->userPreferencesRepository->getPrefValueByIdUserAndPrefKey($idUser, UserPreferenceType::URL_DEFAULT_MODULE);
        if (!$defaultUrl) {
            $defaultUrl = UrlType::RESTRICT;
        }

        return [
            "lang" => $lang,
            UserPreferenceType::URL_DEFAULT_MODULE => $defaultUrl
        ];
    }

}
