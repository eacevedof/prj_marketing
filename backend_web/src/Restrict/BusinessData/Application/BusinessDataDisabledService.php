<?php
namespace App\Restrict\BusinessData\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;

final class BusinessDataDisabledService extends AppService
{
    public function __invoke(int $idowner): bool
    {
        return RF::get(BusinessDataRepository::class)->is_disabled_by_iduser($idowner);
    }

    public function get_disabled_data(int $idowner): array
    {
        return RF::get(BusinessDataRepository::class)->get_disabled_data_by_iduser($idowner);
    }
}