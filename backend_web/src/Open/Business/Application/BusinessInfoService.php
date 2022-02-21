<?php
namespace App\Open\Business\Application;

use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class BusinessInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private UserRepository $repouser;
    private BusinessDataRepository $repobusinessdata;

    public function __construct()
    {
        $this->repouser = RF::get(UserRepository::class);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
    }

    public function __invoke(): array
    {
        return [
            "promotion" => [],
            "businessdata" => [],
            "metadata" => [], //depende si es test o no
        ];
    }
}