<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Open\PromotionCaps\Domain\PromotionCapUsersRepository
 * @file PromotionCapUsersRepository.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Open\PromotionCaps\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType as Status;
use TheFramework\Components\Db\ComponentQB;

final class PromotionCapUsersRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
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

    private function _add_auth_condition(ComponentQB $qb): void
    {
        if (!$this->auth->get_user()) {
            $qb->add_and("1 = 0");
            return;
        }

        if($this->auth->is_root()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        //como no es root no puede ver borrados o desactivados
        $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");

        $autuser = $this->auth->get_user();
        if($this->auth->is_business_owner()) {
            $qb->add_andoper("m.id_owner", $autuser["id"]);
            return;
        }

        if($this->auth->is_business_manager()) {
            $idparent = $autuser["id_parent"];
            $qb->add_andoper("m.id_owner", $idparent);
        }
    }

    public function search(array $search): array
    {
        $qb = $this->_get_qbuilder()
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
            ->set_orderby(["m.id"=>"DESC"])
        ;
        $this->_add_joins($qb);
        $this->_add_search_filter($qb, $search);
        $this->_add_auth_condition($qb);

        $sql = $qb->select()->sql();
        $sqlcount = $qb->sqlcount();
        $r = $this->query_with_count($sqlcount, $sql);
        return $r;
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
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
        if (!$r) return [];

        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function is_subscribed_by_email(int $idpromotion, string $email): bool
    {
        $email = $this->get_sanitized($email);
        $sql = $this->_get_qbuilder()
            ->set_comment("promocapusers.is_subscribed")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id", "ps.subs_status"])
            ->add_join("INNER JOIN app_promotioncap_subscriptions ps ON m.id = ps.id_promouser")
            ->add_and("m.delete_date IS NULL")
            ->add_and("ps.delete_date IS NULL")
            ->add_and("ps.is_test=0")
            ->add_and("m.id_promotion=$idpromotion")
            ->add_and("m.email='$email'")
            ->add_orderby("ps.id", "DESC")
            ->set_limit(1)
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if (!$r) return false;

        $status = $r[0]["subs_status"];
        if (in_array($status, [Status::EXECUTED])) {
            return false;
        }

        return true;
    }

    public function get_subscription_data(int $idpromouser): array
    {
        $sql = $this->_get_qbuilder()
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
            ->add_and("pu.id=$idpromouser")
            ->add_and("pu.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function get_points_by_email_in_account(string $email, int $idowner): array
    {
        $email = $this->get_sanitized($email);
        $sql = $this->_get_qbuilder()
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
            ->add_and("m.id_owner=$idowner")
            ->add_and("m.email='$email'")
            ->add_and("ps.is_test=0")
            ->add_and("p.id_owner=$idowner")
            ->add_and("ps.date_execution IS NOT NULL")
            ->add_and("COALESCE(ps.description,'') NOT LIKE '%consumed%'")
            ->add_orderby("ps.date_execution","DESC")
            ->select()->sql()
        ;
        return $this->query($sql);
    }

    public function get_data_by_subsuuid(string $subsuuid): array
    {
        $sql = $this->_get_qbuilder()
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
            ->add_and("ps.uuid='$subsuuid'")
            ->add_and("pu.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }
}
