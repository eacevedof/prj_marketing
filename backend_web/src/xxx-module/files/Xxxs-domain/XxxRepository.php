<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\App\XxxRepository
 * @file XxxRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\App;

use App\Repositories\AppRepository;
use App\Traits\SearchRepoTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\DbFactory as DbF;
use App\Services\Auth\AuthService;
use TheFramework\Components\Db\ComponentQB;

final class XxxRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "%TABLE%";
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
            ->set_comment("xxx.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                %SEARCH_FIELDS%,
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
            ->set_comment("xxx.get_info(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields([
                %INFO_FIELDS%
            ])
            //->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        if (!$r) return [];

        $sysdata = RF::get("Common\Sysfield")->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

}//XxxRepository
