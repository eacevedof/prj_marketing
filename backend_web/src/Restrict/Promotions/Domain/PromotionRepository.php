<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionRepository
 * @file PromotionRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 */
namespace App\Restrict\Promotions\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use TheFramework\Components\Db\ComponentQB;

final class PromotionRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "app_promotion";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
                "u3.description"  => "e_owner",
                "ar1.description" => "e_is_published",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "LEFT JOIN base_user u3 ON m.id_owner = u3.id",
                "LEFT JOIN app_array ar1 ON m.is_published = ar1.id_pk AND ar1.type='bool'",
                "LEFT JOIN app_array ar2 ON m.id_tz = ar2.id_pk AND ar2.type='tz'",
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

        $authuser = $this->auth->get_user();
        if($this->auth->is_business_owner()) {
            $qb->add_andoper("m.id_owner", $authuser["id"]);
            return;
        }

        if($this->auth->is_business_manager()) {
            $idparent = $authuser["id_parent"];
            $qb->add_andoper("m.id_owner", $idparent);
        }
    }

    public function search(array $search): array
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("promotion.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.slug",
                //"m.date_from",
                "CONVERT_TZ(m.date_from, 'UTC', COALESCE(TRIM(ar2.description),'UTC')) date_from",
                //"m.date_to",
                "CONVERT_TZ(m.date_to, 'UTC', COALESCE(TRIM(ar2.description),'UTC')) date_to",
                "m.content",
                /*
                "m.bgcolor",
                "m.bgimage_xs",
                "m.bgimage_sm",
                "m.bgimage_md",
                "m.bgimage_lg",
                "m.bgimage_xl",
                "m.bgimage_xxl",
                */
                "m.is_published",
                "m.invested",
                "m.returned",
                "m.max_confirmed",
                "m.notes",
                "m.delete_date",
            ])
            ->set_limit(25)
            ->set_orderby(["m.id"=>"DESC"])
        ;
        $this->_add_joins($qb);
        $this->_add_search_filter($qb, $search);
        $this->_add_auth_condition($qb);

        $sql = $qb->select()->sql();
        $sqlcount = $qb->sqlcount();
        $r = $this->db->set_sqlcount($sqlcount)->query($sql);

        return [
            "result" => $r,
            "total" => $this->db->get_foundrows()
        ];
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $qb = $this->_get_qbuilder()
            ->set_comment("promotion.get_info(uuid)")
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
                "m.id_tz",
                "m.code_erp",
                "m.description",
                "m.slug",
                "m.date_from",
                "m.date_to",
                "m.content",
                "m.bgcolor",
                "m.bgimage_xs",
                "m.bgimage_sm",
                "m.bgimage_md",
                "m.bgimage_lg",
                "m.bgimage_xl",
                "m.bgimage_xxl",
                "m.invested",
                "m.returned",
                "m.max_confirmed",
                "m.is_raffleable",
                "m.is_cumulative",
                "m.is_published",
                "m.is_launched",
                "m.tags",
                "m.notes",
                "m.num_viewed",
                "m.num_subscribed",
                "m.num_confirmed",
                "m.num_executed"
            ])
            ->add_and("m.uuid='$uuid'")
        ;
        $this->_add_joins($qb);
        $sql = $qb->select()->sql();
        $r = $this->db->query($sql);
        if (!$r) return [];

        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

    public function is_launched_by_uuid(string $uuid): bool
    {
        $uuid = $this->_get_sanitized($uuid);
        $qb = $this->_get_qbuilder()
            ->set_comment("promotion.is_launched_by_uuid")
            ->set_table("$this->table as m")
            ->set_getfields(["m.is_launched",])
            ->add_and("m.uuid='$uuid'");
        $sql = $qb->select()->sql();
        $r = $this->db->query($sql);
        return (bool) $r[0]["is_launched"];
    }

    public function update_slug_with_id(int $id): void
    {
        $sql = "UPDATE $this->table SET slug=CONCAT(slug,'-', id) WHERE id=$id";
        $this->db->exec($sql);
    }
}
