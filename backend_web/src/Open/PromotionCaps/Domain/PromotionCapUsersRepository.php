<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Open\PromotionCaps\Domain\PromotionCapUsersRepository
 * @file PromotionCapUsersRepository.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Open\PromotionCaps\Domain;

use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType as Status;
use App\Shared\Infrastructure\Factories\{DbFactory as DbF, RepositoryFactory as RF};

final class PromotionCapUsersRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_promotioncap_users";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
            ]
        ];
    }

    private function _addAuthCondition(ComponentQB $queryBuilder): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $queryBuilder->add_and("1 = 0");
            return;
        }

        if ($this->authService->isAuthUserRoot()) {
            $queryBuilder->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        //como no es root no puede ver borrados o desactivados
        $queryBuilder->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");

        $authUser = $this->authService->getAuthUserArray();
        if ($this->authService->isAuthUserBusinessOwner()) {
            $queryBuilder->add_andoper("m.id_owner", $authUser["id"]);
            return;
        }

        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $authUser["id_parent"];
            $queryBuilder->add_andoper("m.id_owner", $idParent);
        }
    }

    public function search(array $search): array
    {
        $queryBuilder = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.id_promotion",
                "m.id_language",
                "m.id_country",
                "m.phone1",
                "m.email",
                "m.birthdate",
                "m.name1",
                "m.name2",
                "m.id_gender",
                "m.address",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id" => "DESC"])
        ;
        $this->_addJoinsToQueryBuilder($queryBuilder);
        $this->_addSearchFilterToQueryBuilder($queryBuilder, $search);
        $this->_addAuthCondition($queryBuilder);

        $sql = $queryBuilder->select()->sql();
        $sqlCount = $queryBuilder->sqlcount();
        $r = $this->getQueryWithCount($sqlCount, $sql);
        return $r;
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_getSanitizedString($uuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.get_info(uuid)")
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
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.id_promotion",
                "m.id_language",
                "m.id_country",
                "m.phone1",
                "m.email",
                "m.birthdate",
                "m.name1",
                "m.name2",
                "m.id_gender",
                "m.address"
            ])
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);

        $this->mapFieldsToInt($r, ["id", "id_owner", "id_promotion", "id_language", "id_country", "id_gender"]);
        if (!$r) {
            return [];
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);
        return array_merge($r, $sysData);
    }

    public function isSubscribedByIdPromotionAndEmail(int $idPromotion, string $email): bool
    {
        $email = $this->_getSanitizedString($email);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.is_subscribed")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id", "ps.subs_status"])
            ->add_join("INNER JOIN app_promotioncap_subscriptions ps ON m.id = ps.id_promouser")
            ->add_and("m.delete_date IS NULL")
            ->add_and("ps.delete_date IS NULL")
            ->add_and("ps.is_test=0")
            ->add_and("m.id_promotion=$idPromotion")
            ->add_and("m.email='$email'")
            ->add_orderby("ps.id", "DESC")
            ->set_limit(1)
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if (!$r) {
            return false;
        }

        $status = $r[0]["subs_status"];
        if (in_array($status, [Status::EXECUTED])) {
            return false;
        }

        return true;
    }

    public function getSubscriptionData(int $promoUserId): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.get_subscription_data")
            ->set_table("$this->table as pu")
            ->set_getfields([
                "pu.id AS idcapuser, pu.uuid AS capusercode, pu.email, pu.name1 AS username",
                "ps.id AS subsid, ps.uuid AS subscode, ps.date_confirm, ps.date_execution, ps.code_execution AS execode",
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.url_business AS businessurl, bd.user_logo_1 AS businesslogo",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",
                "p.uuid AS promocode, p.slug AS promoslug, p.description AS promotion, p.content AS promoterms, p.date_to AS promodateto, p.date_execution as promodateexec",
                "p.bgimage_xs AS promoimage"
            ])
            ->add_join("INNER JOIN app_promotioncap_subscriptions AS ps
            ON pu.id = ps.id_promouser
            AND ps.id_promotion = pu.id_promotion")
            ->add_join("INNER JOIN app_promotion AS p
            ON pu.id_promotion = p.id")
            ->add_join(" INNER JOIN app_business_data AS bd
            ON p.id_owner = bd.id_user")
            ->add_and("pu.id=$promoUserId")
            ->add_and("pu.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["idcapuser", "subsid"]);
        return $r[0] ?? [];
    }

    public function getPointsInAccountByEmailAndIdOwner(string $email, int $idOwner): array
    {
        $email = $this->_getSanitizedString($email);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.get_points_by_email_in_account")
            ->set_table("$this->table as m")
            ->distinct()
            ->set_getfields(["p.description, ps.uuid, ps.date_execution, 1 AS p"])
            ->add_join("
            INNER JOIN app_promotioncap_subscriptions AS ps 
            ON m.id = ps.id_promouser AND m.id_promotion = ps.id_promotion
            ")
            ->add_join("LEFT JOIN app_promotion AS p ON ps.id_promotion = p.id")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_owner=$idOwner")
            ->add_and("m.email='$email'")
            ->add_and("ps.is_test=0")
            ->add_and("p.id_owner=$idOwner")
            ->add_and("ps.date_execution IS NOT NULL")
            ->add_and("COALESCE(ps.description,'') NOT LIKE '%consumed%'")
            ->add_orderby("ps.date_execution", "DESC")
            ->select()->sql()
        ;
        return $this->query($sql);
    }

    public function getDataByPromotionCapSubscriptionUuid(string $promoCapSubscriptionUuid): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promocapusers.get_subscription_data")
            ->set_table("$this->table as pu")
            ->set_getfields([
                "pu.id AS idcapuser, pu.uuid AS capusercode, pu.email, pu.name1 AS username",
                "ps.id AS subsid, ps.uuid AS subscode, ps.date_confirm, ps.date_execution, ps.code_execution AS execode",
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.user_logo_1 AS businesslogo",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",
                "p.uuid AS promocode, p.slug AS promoslug, p.description AS promotion",
            ])
            ->add_join("INNER JOIN app_promotioncap_subscriptions AS ps
            ON pu.id = ps.id_promouser
            AND ps.id_promotion = pu.id_promotion")
            ->add_join("INNER JOIN app_promotion AS p
            ON pu.id_promotion = p.id")
            ->add_join(" INNER JOIN app_business_data AS bd
            ON p.id_owner = bd.id_user")
            ->add_and("ps.uuid='$promoCapSubscriptionUuid'")
            ->add_and("pu.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["idcapuser"]);
        return $r[0] ?? [];
    }
}
