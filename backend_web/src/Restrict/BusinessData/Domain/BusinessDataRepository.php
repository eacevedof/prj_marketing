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
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use TheFramework\Components\Db\ComponentQB;

final class BusinessDataRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
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
            ->set_comment("businessdata.get_info(uuid)")
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
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if (!$r) return [];

        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

    public function get_by_user(int $iduser, array $fields=[]): array
    {
        $type = AppArrayType::TIMEZONE;
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_by_user")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*", "ar1.description AS e_timezone"])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_tz = ar1.id_pk AND ar1.type='$type'")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
        ;
        if ($fields) $sql->set_getfields($fields);

        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }

    public function get_by_slug(string $slug, array $fields=[]): array
    {
        $slug = $this->get_sanitized($slug);
        $type = AppArrayType::TIMEZONE;
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_by_slug")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_tz = ar1.id_pk AND ar1.type='$type'")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.slug='$slug'")
            ->set_limit(1);
        if ($fields) $sql->set_getfields($fields);
        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }

    public function get_space_by_promotion(string $promouuid): array
    {
        $promouuid = $this->_get_sanitized($promouuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_space_by_promotion")
            ->set_table("$this->table as bd")
            ->set_getfields([
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.url_business AS businessurl, bd.user_logo_1 AS businesslogo",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",

                "p.uuid AS promocode, p.slug AS promoslug, p.description AS promotion, p.content AS promoterms, p.date_to AS promodateto",
                "p.bgimage_xs AS promoimage"
            ])
            ->add_join("INNER JOIN app_promotion AS p ON p.id_owner = bd.id_user")
            ->add_and("p.uuid='$promouuid'")
            ->add_and("p.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function get_space_by_uuid(string $businessuuid): array
    {
        $businessuuid = $this->_get_sanitized($businessuuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_space_by_uuid")
            ->set_table("$this->table as bd")
            ->set_getfields([
                "bd.uuid AS businesscode, bd.slug AS businessslug, bd.business_name AS business, bd.url_business AS businessurl, bd.user_logo_1 AS businesslogo",
                "bd.url_social_fb AS urlfb, bd.url_social_ig AS urlig, bd.url_social_twitter AS urltwitter, bd.url_social_tiktok AS urltiktok",
            ])
            ->add_and("bd.uuid='$businessuuid'")
            ->add_and("bd.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function is_disabled_by_iduser(int $iduser): bool
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.is_disabled_by_iduser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.disabled_date IS NOT NULL")
            ->add_and("m.id_user=$iduser")
        ;
        $sql = $sql->select()->sql();
        return (bool) ($this->query($sql)[0]["id"] ?? null);
    }

    public function get_disabled_data_by_iduser(int $iduser): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_disabled_data_by_iduser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.business_name","m.disabled_date","m.disabled_user","m.disabled_reason"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.disabled_date IS NOT NULL")
            ->add_and("m.id_user=$iduser")
        ;
        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }

    public function get_top5_last_running_promotions_by_slug(string $businessslug): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("businessdata.get_disabled_data_by_iduser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.business_name","m.disabled_date","m.disabled_user","m.disabled_reason"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.disabled_date IS NOT NULL")
            ->add_and("m.id_user=$iduser")
        ;
        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }
}
