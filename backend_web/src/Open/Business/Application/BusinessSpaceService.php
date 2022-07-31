<?php
namespace App\Open\Business\Application;

use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;

final class BusinessSpaceService extends AppService
{
    private bool $istest;

    //"_test_mode" => $this->request->get_get("mode", "")==="test",
    public function __construct(array $input=[])
    {
        $this->istest = $input["_test_mode"] ?? false;
    }

    public function get_data_by_promotion(string $promouuid): array
    {
        $space =  RF::get(BusinessDataRepository::class)->get_space_by_promotion($promouuid);
        if (!$space) return [];

        $url = Routes::url("subscription.create", [
            "businessslug"=>$space["businessslug"],
            "promotionslug"=>$space["promoslug"]
        ]);

        if ($this->istest) $url .= "?mode=test";
        $space["promotionlink"] = UrlDomainHelper::get_instance()->get_full_url($url);
        return $space;
    }

    public function get_data_by_promotion_slug(string $promoslug): array
    {
        $promouuid = RF::get(PromotionRepository::class)->get_by_slug($promoslug, ["uuid"]);
        if (!$promouuid) return [];
        return $this->get_data_by_promotion($promouuid["uuid"]);
    }

    public function get_data_by_uuid(string $businessuuid): array
    {
        return RF::get(BusinessDataRepository::class)->get_space_by_uuid($businessuuid);
    }

    public function get_promotion_url(string $promouuid): string
    {
        $space =  RF::get(BusinessDataRepository::class)->get_space_by_promotion($promouuid);
        if (!$space) return [];

        $url = Routes::url("subscription.create", [
            "businessslug"=>$space["businessslug"],
            "promotionslug"=>$space["promoslug"]
        ]);
        return $this->istest ? "$url?mode=test" : $url;
    }
}