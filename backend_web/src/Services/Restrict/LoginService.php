<?php
namespace App\Services\Restrict;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Factories\RepositoryFactory as RF;
use \Exception;

final class LoginService extends AppService
{
    use SessionTrait;
    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private const URL_LOGOUT = "/login";

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
    }

    public function in(): void
    {
        $email = $this->input["email"];
        if (!$email) $this->_exeption(__("Empty email"));

        $password = $this->input["password"];
        if (!$password) $this->_exeption(__("Empty password"));

        $aruser = $this->repository->by_email($email);
        if (!($secret = $aruser["secret"])) $this->_exeption(__("Invalid data"));

        if (!$this->encdec->check_hashpassword($password, $secret))
            $this->_exeption(__("Unauthorized"));

        $this->session->add("auth_user", $aruser);
        $this->session->add("lang", $aruser["language"] ?? "en");

        //die();
    }

    public function out(): void
    {
        $this->session->destroy();
        header("Location: /login");
    }
}