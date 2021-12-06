<?php
namespace App\Services\Common;
use App\Services\AppService;
use App\Repositories\App\PicklistRepository;
use App\Factories\RepositoryFactory as RF;


final class PicklistService extends AppService
{
    private PicklistRepository $repository;

    public function __construct()
    {
        $this->repository = RF::get("App/Picklist");
    }

    public function get_languages(): array
    {
        return $this->repository->get_languages();
    }

    public function get_profiles(): array
    {
        return $this->repository->get_profiles();
    }

    public function get_countries(): array
    {
        return $this->repository->get_countries();
    }
}