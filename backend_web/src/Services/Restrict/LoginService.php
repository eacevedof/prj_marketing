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

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
    }

    public function access(): void
    {
        $email = $this->input["email"];
        if (!$email) $this->_exeption(__("Empty email"));

        $password = $this->input["password"];
        if (!$password) $this->_exeption(__("Empty password"));

        $aruser = $this->repository->by_email($email);
        if (!($secret = $aruser["secret"])) $this->_exeption(__("Invalid data"));

        if ($this->encdec->check_hashpassword($password, $secret)) {
            $this->session->add("auth_user", $aruser);
        }
    }
}