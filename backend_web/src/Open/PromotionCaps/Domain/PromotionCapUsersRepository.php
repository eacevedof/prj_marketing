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
            ->set_comment("promotioncap_users.search")
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
        $r = $this->db->set_sqlcount($sqlcount)->query($sql);

        return [
            "result" => $r,
            "total" => $this->db->get_foundrows()
        ];
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("promotioncap_users.get_info(uuid)")
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
            //->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        if (!$r) return [];

        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function is_subscribed_by_email(int $idpromotion, string $email): bool
    {
        $email = $this->get_sanitized($email);
        $sql = $this->_get_qbuilder()
            ->set_comment("promotioncapsubscriptions.is_subscribed")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.id_promotion=$idpromotion")
            ->add_and("m.email='$email'")
            ->add_and("m.delete_date IS NULL")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return (bool) ($r[0]["id"] ?? null);
    }
    public function get_data_for_mail(int $idpromouser): int
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("promotioncapsubscriptions.get_num_confirmed")
            ->set_table("$this->table as pu")
            ->set_getfields([
                "bd.business_name, pu.name1, pu.email, p.description AS promotion, p.uuid promocode, ps.uuid AS promolink"
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
        $r = $this->db->query($sql);
        return (int) ($r[0]["num_confirmed"] ?? 0);
    }
}
