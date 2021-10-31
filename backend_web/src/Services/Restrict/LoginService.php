<?php
namespace App\Services\Restrict;
use App\Services\AppService;
use \Exception;
use TheFramework\Components\Formatter\ComponentMoment;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncdecrypt;

final class LoginService extends AppService
{
    private array $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function access(): void
    {}
}