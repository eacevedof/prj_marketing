<?php
namespace App\Services\Restrict;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Factories\RepositoryFactory as RF;
use App\Enums\Key;
use \Exception;

final class LoginService extends AppService
{
    use SessionTrait;
    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;
    private const URL_LOGOUT = "/login";

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function in(): void
    {
        $email = $this->input["email"];
        if (!$email) $this->_exeption(__("Empty email"));

        $password = $this->input["password"];
        if (!$password) $this->_exeption(__("Empty password"));

        $aruser = $this->repository->get_by_email($email);
        if (!($secret = $aruser["secret"])) $this->_exeption(__("Invalid data"));

        if (!$this->encdec->check_hashpassword($password, $secret))
            $this->_exeption(__("Unauthorized"));

        $this->session->add(Key::AUTH_USER, $aruser);
        $this->session->add(Key::LANG, $aruser["language"] ?? "en");

        $permissions = $this->permissionrepo->get_by_user($aruser["id"]);
        $this->session->add(Key::AUTH_USER_PERMISSIONS, $permissions);
    }
}