<?php
namespace App\Services\Restrict;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\BaseUserRepository;
use App\Traits\SessionTrait;
use \Exception;

final class LoginService extends AppService
{
    use SessionTrait;
    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private BaseUserRepository $baseUserRepository;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->encdec = $this->_get_encdec();
        $this->baseUserRepository = new BaseUserRepository();
    }

    public function access(): void
    {
        $this->baseUserRepository->get_all();
    }
}