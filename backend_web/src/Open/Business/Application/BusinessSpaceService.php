<?php
namespace App\Open\Business\Application;

use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;

final class BusinessSpaceService extends AppService
{
    private bool $istest;

    //"_test_mode" => $this->request->get_get("mode", "")==="test",
    public function __construct(array $input)
    {
        $this->istest = $input["_test_mode"] ?? false;
    }

    public function get_data_by_promotion(string $promouuid): array
    {
        $space =  RF::get(BusinessDataRepository::class)->get_space_by_promotion($promouuid);
        if (!$space) return [];

        $url = "promotion/{$space["businessslug"]}/{$space["promoslug"]}";
        if ($this->istest) $url .= "?mode=test";
        $space["promolink"] = UrlDomainHelper::get_instance()->get_full_url($url);
        return $space;
    }
}