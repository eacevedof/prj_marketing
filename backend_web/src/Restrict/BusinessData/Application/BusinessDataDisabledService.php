<?php

namespace App\Restrict\BusinessData\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;

final class BusinessDataDisabledService extends AppService
{
    public function __invoke(int $idOwner): bool
    {
        return RF::getInstanceOf(BusinessDataRepository::class)->isBusinessDataDisabledByIdUser($idOwner);
    }

    public function getDisabledDataByUser(int $idOwner): array
    {
        return RF::getInstanceOf(BusinessDataRepository::class)->getDisabledBusinessDataByIdUser($idOwner);
    }
}
