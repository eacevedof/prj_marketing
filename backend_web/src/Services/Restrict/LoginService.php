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
    private UserRepository $baseUserRepository;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->encdec = $this->_get_encdec();
        $this->baseUserRepository = RF::get("Base/User");
    }

    public function access(): void
    {
        $this->baseUserRepository->get_all();
    }
}