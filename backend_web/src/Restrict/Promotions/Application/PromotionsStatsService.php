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
        $auth = SF::get_auth();
        if(!(
            $auth->is_user_allowed(UserPolicyType::PROMOTION_STATS_READ)
            || $auth->is_user_allowed(UserPolicyType::PROMOTION_STATS_WRITE)
        ))
            return null;
        $stats = RF::get(PromotionRepository::class)->get_statistics_by_uuid($this->input["uuid"]);
        return [
            "viewed" => 0,
            "subscribed" => 10,
            "confirmed" => 5,
            "executed" => 2,
        ];
    }
}