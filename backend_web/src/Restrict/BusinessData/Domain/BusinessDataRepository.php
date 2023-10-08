<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\BusinessData\Domain\BusinessDataRepository
 * @file BusinessDataRepository.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\BusinessData\Domain;

use App\Picklist\Domain\Enums\AppArrayType;
use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Shared\Infrastructure\Factories\{
    DbFactory as DbF,
    RepositoryFactory as RF
};

final class BusinessDataRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_business_data";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
                //"ar1.description" => "e_language",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                //"LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'",
            ]
        ];
    }

    private function _addConditionByAuthService(ComponentQB $qb): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $qb->add_and("1 = 0");
            return;
        }

        if ($this->authService->isAuthUserRoot()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        //como no es root no puede ver borrados o desactivados
        $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");

        $authUser = $this->authService->getAuthUserArray();
        if ($this->authService->isAuthUserBusinessOwner()) {
            $qb->add_andoper("m.id_owner", $authUser["id"]);
            return;
        }

        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $authUser["id_parent"];
            $qb->add_andoper("m.id_owner", $idParent);
        }
    }

    public function search(array $search): array
    {
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_user",
                "m.slug",
                "m.user_logo_1",
                "m.user_logo_2",
                "m.user_logo_3",
                "m.url_favicon",
                "m.head_bgcolor",
                "m.head_color",
                "m.head_bgimage",
                "m.body_bgcolor",
                "m.body_color",
                "m.body_bgimage",
                "m.site",
                "m.url_social_fb",
                "m.url_social_ig",
                "m.url_social_twitter",
                "m.url_social_tiktok",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id" => "DESC"])
        ;
        $this->_addJoinsToQueryBuilder($qb);
        $this->_addSearchFilterToQueryBuilder($qb, $search);
        $this->_addConditionByAuthService($qb);

        $sql = $qb->select()->sql();
        $sqlCount = $qb->sqlcount();
        $r = $this->getQueryWithCount($sqlCount, $sql);
        return $r;
    }

    public function getBusinessDataInfoByBusinessDataUuid(string $businessDataUuid): array
    {
        $businessDataUuid = $this->_getSanitizedString($businessDataUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.getBusinessDataInfoByBusinessDataUuid")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.insert_user",
                "m.insert_date",
                "m.update_user",
                "m.update_date",
                "m.delete_user",
                "m.delete_date",
                "m.id",
                "m.uuid",
                "m.id_user",
                "m.slug",
                "m.user_logo_1",
                "m.user_logo_2",
                "m.user_logo_3",
                "m.url_favicon",
                "m.head_bgcolor",
                "m.head_color",
                "m.head_bgimage",
                "m.body_bgcolor",
                "m.body_color",
                "m.body_bgimage",
                "m.site",
                "m.url_social_fb",
                "m.url_social_ig",
                "m.url_social_twitter",
                "m.url_social_tiktok"
            ])
            //->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.uuid='$businessDataUuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id", "id_user"]);
        if (!$r) {
            return [];
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);

        return array_merge($r, $sysData);
    }

    public function setAuthService(AuthService $authService): self
    {
        $this->authService = $authService;
        return $this;
    }

    public function getBusinessDataByIdUser(int $idUser, array $fields = []): array
    {
        $type = AppArrayType::TIMEZONE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.get_by_user")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*", "ar1.description AS e_timezone"])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_tz = ar1.id_pk AND ar1.type='$type'")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user = $idUser")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }

        $sql = $sql->select()->sql();

        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id", "id_user", "id_tz"]);
        return $r[0] ?? [];
    }

    public function getBusinessDataByBusinessDataSlug(string $businessDataSlug, array $fields = []): array
    {
        $businessDataSlug = $this->_getSanitizedString($businessDataSlug);
        $type = AppArrayType::TIMEZONE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.get_by_slug")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_tz = ar1.id_pk AND ar1.type='$type'")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.slug = '$businessDataSlug'")
            ->set_limit(1);
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();

        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id", "id_user", "id_tz"]);
        return $r[0] ?? [];
    }

    public function getSpaceByPromotionUuid(string $promotionUuid): array
    {
        $promotionUuid = $this->_getSanitizedString($promotionUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.get_space_by_promotion")
            ->set_table("$this->table as bd")
            ->set_getfields([
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.url_business AS businessurl, bd.user_logo_1 AS businesslogo",
                "bd.url_favicon AS businessfavicon, bd.body_bgimage AS businessbgimage",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",

                "p.uuid AS promocode, p.slug AS promoslug, p.description AS promotion, p.content AS promoterms, p.date_to AS promodateto",
                "p.bgimage_xs AS promoimage"
            ])
            ->add_join("INNER JOIN app_promotion AS p ON p.id_owner = bd.id_user")
            ->add_and("p.uuid = '$promotionUuid'")
            ->add_and("bd.delete_date IS NULL")
            ->add_and("p.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function getSpaceByBusinessUuid(string $businessUuid): array
    {
        $businessUuid = $this->_getSanitizedString($businessUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.get_space_by_uuid")
            ->set_table("$this->table as bd")
            ->set_getfields([
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.url_business AS businessurl, bd.user_logo_1 AS businesslogo",
                "bd.url_favicon AS businessfavicon, bd.body_bgimage AS businessbgimage",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",
            ])
            ->add_and("bd.uuid='$businessUuid'")
            ->add_and("bd.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function isBusinessDataDisabledByIdUser(int $idUser): bool
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.isBusinessDataDisabledByIdUser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.disabled_date IS NOT NULL")
            ->add_and("m.id_user=$idUser")
        ;
        $sql = $sql->select()->sql();
        return (bool) ($this->query($sql)[0]["id"] ?? null);
    }

    public function getDisabledBusinessDataByIdUser(int $idUser): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.getDisabledBusinessDataByIdUser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.business_name","m.disabled_date","m.disabled_user","m.disabled_reason"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.disabled_date IS NOT NULL")
            ->add_and("m.id_user = $idUser")
        ;
        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }

    public function getTop5LastRunningPromotionsByBusinessSlug(string $businessSlug, string $tz = "UTC"): array
    {
        $businessSlug = $this->_getSanitizedString($businessSlug);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("businessdata.getTop5LastRunningPromotionsByBusinessSlug")
            ->set_table("$this->table as m")
            ->set_getfields(["p.slug, p.description, p.bgimage_xs, CONVERT_TZ(p.date_from,'UTC','$tz') date_from, CONVERT_TZ(p.date_to,'UTC','$tz') date_to"])
            ->add_join("INNER JOIN app_promotion p ON m.id_user = p.id_owner")
            ->add_and("m.slug = '$businessSlug'")
            ->add_and("m.delete_date IS NULL")
            ->add_and("p.delete_date IS NULL")
            ->add_and("m.disabled_date IS NULL")
            ->add_and("p.disabled_date IS NULL")
            ->add_and("p.is_published = 1")
            ->add_and("(p.num_confirmed < p.max_confirmed OR p.max_confirmed=-1)")
            ->add_and("p.date_from <= UTC_TIMESTAMP()")
            ->add_and("p.date_to >= UTC_TIMESTAMP()")
            ->add_orderby("p.date_to")
            ->add_orderby("p.description")
            ->set_limit(5)
        ;
        $sql = $sql->select()->sql();
        return $this->query($sql);
    }
}
