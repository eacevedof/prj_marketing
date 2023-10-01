<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsStatsService extends AppService
{
    public function __construct(array $input)
    {
        $this->input["uuid"] = $input["uuid"] ?? "";
    }

    public function __invoke(): ?array
    {
        if (!SF::getAuthService()->hasAuthUserPolicy(UserPolicyType::PROMOTION_STATS_READ)) {
            return null;
        }

        $stats = RF::getInstanceOf(PromotionRepository::class)->getPromotionCapStatisticsByPromotionUuid($this->input["uuid"]);

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
