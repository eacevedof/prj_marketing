<?php
namespace App\Services\Restrict;
use App\Repositories\Base\PicklistRepository;
use App\Services\AppService;
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
}