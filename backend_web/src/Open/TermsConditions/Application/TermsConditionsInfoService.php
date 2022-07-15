<?php
namespace App\Open\TermsConditions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory;
use App\Restrict\Promotions\Domain\PromotionRepository;

final class TermsConditionsInfoService extends AppService
{
    public function __construct(array $input)
    {
        $this->input = $input["promoslug"] ?? "";
    }

    public function __invoke(): array
    {
        return [];
    }

    public function get_by_promotion(): array
    {
        return [
            ["h2" => ""]
        ];
    }
}