<?php

namespace App\Open\Business\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Helpers\{
    RoutesHelper as Routes,
    UrlDomainHelper
};
use App\Open\PromotionCaps\Domain\{
    PromotionCapSubscriptionsRepository,
    PromotionCapUsersRepository
};

final class BusinessSpaceService extends AppService
{
    private int $isTestMode;

    //"_test_mode" => $this->request->get_get("mode", "")==="test",
    public function __construct(array $input = [])
    {
        $this->isTestMode = (int) ($input["_test_mode"] ?? "");
    }

    public function getBusinessDataByPromotionUuid(string $promotionUuid): array
    {
        $space =  RF::getInstanceOf(BusinessDataRepository::class)->getSpaceByPromotionUuid($promotionUuid);
        if (!$space) {
            return [];
        }

        $url = Routes::getUrlByRouteName("subscription.create", [
            "businessSlug" => $space["businessslug"],
            "promotionSlug" => $space["promoslug"]
        ]);

        if ($this->isTestMode) {
            $url .= "?mode=test";
        }
        $space["promotionlink"] = UrlDomainHelper::getInstance()->getDomainUrlWithAppend($url);
        return $space;
    }

    public function getDataByPromotionSlug(string $promotionSlug): array
    {
        $promotionUuid = RF::getInstanceOf(PromotionRepository::class)->getPromotionByPromotionSlug($promotionSlug, ["uuid"]);
        if (!$promotionUuid) {
            return [];
        }
        return $this->getBusinessDataByPromotionUuid($promotionUuid["uuid"]);
    }

    public function getDataByPromotionCapByPromotionCapUuid(string $promotionCapUuid): array
    {
        $idPromotion = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class)->getEntityByEntityUuid(
            $promotionCapUuid,
            ["id_promotion"]
        )["id_promotion"] ?? 0;

        $promotionUUid = RF::getInstanceOf(PromotionRepository::class)->getEntityByEntityId($idPromotion, ["uuid"]);
        if (!$promotionUUid) {
            return [];
        }
        return $this->getBusinessDataByPromotionUuid($promotionUUid["uuid"]);
    }

    public function getDataByPromotionCapUser(string $promotionCapUserUuid): array
    {
        $idPromotion = RF::getInstanceOf(PromotionCapUsersRepository::class)->getEntityByEntityUuid(
            $promotionCapUserUuid,
            ["id_promotion"]
        )["id_promotion"] ?? 0;
        $promotionUuid = RF::getInstanceOf(PromotionRepository::class)->getEntityByEntityId($idPromotion, ["uuid"]);
        if (!$promotionUuid) {
            return [];
        }
        return $this->getBusinessDataByPromotionUuid($promotionUuid["uuid"]);
    }

    public function getBusinessDataByBusinessSlug(string $businessSlug): array
    {
        $bd = RF::getInstanceOf(BusinessDataRepository::class)->getBusinessDataByBusinessDataSlug(
            $businessSlug,
            [
                "slug", "business_name", "url_business", "url_favicon", "user_logo_1", "url_social_fb", "url_social_ig",
                "url_social_twitter", "url_social_tiktok","body_bgimage"
            ]
        );
        if (!$bd) {
            throw new NotFoundException(__("Partner “{0}“ not found!", $businessSlug));
        }

        return [
            "business" => $bd["business_name"],
            //"businessurl" => Routes::url("business.space", ["businessSlug" =>$bd["slug"]]),
            //quiza conviene usar la url de su sitio original en el logo y en los restantes usar el del espacio
            "businessSlug" => $bd["slug"],
            "businessfavicon" => $bd["url_favicon"],
            "businessurl" => $bd["url_business"],
            "businesslogo" => $bd["user_logo_1"],
            "businessbgimage" => $bd["body_bgimage"],
            "urlfb" => $bd["url_social_fb"],
            "urlig" => $bd["url_social_ig"],
            "urltwitter" => $bd["url_social_twitter"],
            "urltiktok" => $bd["url_social_tiktok"],
        ];
    }

    public function getPromotionUrlByPromotionUuid(string $promotionUuid): string
    {
        $space =  RF::getInstanceOf(BusinessDataRepository::class)->getSpaceByPromotionUuid($promotionUuid);
        if (!$space) {
            return "";
        }

        $url = Routes::getUrlByRouteName("subscription.create", [
            "businessSlug" => $space["businessslug"],
            "promotionSlug" => $space["promoslug"]
        ]);
        return $this->isTestMode ? "$url?mode=test" : $url;
    }
}
