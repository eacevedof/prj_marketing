<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;

final class PromotionsStatsService extends AppService
{
    public function __construct(array $input)
    {
        $this->input["uuid"] = $input["uuid"] ?? "";
    }

    public function __invoke(): ?array
    {
        if(!SF::get_auth()->is_user_allowed(UserPolicyType::PROMOTION_STATS_READ))
            return null;

        $stats = RF::get(PromotionRepository::class)->get_statistics_by_uuid($this->input["uuid"]);

        $final = [];
        foreach (array_column($stats, "viewed") as $type) {
            foreach ($stats as $row) {
                if ($row["viewed"] === $type) {
                    $final[$type] = $row["n"];
                }
            }
        }

        return $final;
    }
}