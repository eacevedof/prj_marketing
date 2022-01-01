<?php
namespace App\Services\Restrict;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;
use App\Enums\KeyType;

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
        $this->_sessioninit();

        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function in(): array
    {
        $email = $this->input["email"];
        if (!$email) $this->_exeption(__("Empty email"));

        $password = $this->input["password"];
        if (!$password) $this->_exeption(__("Empty password"));

        $aruser = $this->repository->get_by_email($email);
        if (!($secret = ($aruser["secret"] ?? ""))) $this->_exeption(__("Invalid data"));

        if (!$this->encdec->check_hashpassword($password, $secret))
            $this->_exeption(__("Unauthorized"));

        $aruser[KeyType::AUTH_USER_PERMISSIONS] = $permissions = $this->permissionrepo->get_by_user($aruser["id"]);
        $this->session->add(KeyType::AUTH_USER, $aruser);
        $this->session->add(KeyType::LANG, $lang = ($aruser["e_language"] ?? "en"));
        //$this->session->add(KeyType::AUTH_USER_PERMISSIONS, $permissions);

        return [
            "lang" => $lang
        ];
        //esto no me vale pq la respuesta es ajax y el navegador no escribe
        //$this->cookie->add_value(key::LANG, $lang);
    }
}