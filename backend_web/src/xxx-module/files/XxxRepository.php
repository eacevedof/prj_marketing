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
use App\Factories\RepositoryFactory as RF;
use App\Factories\DbFactory as DbF;
use App\Services\Auth\AuthService;
use TheFramework\Components\Db\ComponentQB;

final class XxxRepository extends AppRepository
{
    private array $joins;
    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "%TABLE%";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
                "ar1.description" => "e_language",
                "ar3.description" => "e_country",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'",
                "LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'",
            ]
        ];
    }

    private function _get_join_field(string $field): string
    {
        $key = array_search($field, $this->joins["fields"]);
        if ($key===false) return "m.$field";
        return $key;
    }

    private function _get_condition(string $field, string $value): string
    {
        $value = $this->_get_qbuilder()->get_sanitized($value);
        $field = $this->_get_join_field($field);
        return "$field LIKE '%$value%'";
    }

    private function _add_joins(ComponentQB $qb): void
    {
        foreach ($this->joins["fields"] as $field => $alias)
            $qb->add_getfield("$field as $alias");

        foreach ($this->joins["on"] as $join)
            $qb->add_join($join);
    }
    
    private function _add_search_filter(ComponentQB $qb, array $search): void
    {
        if(!$search) return;

        if($fields = $search["fields"])
            foreach ($fields as $field => $value )
                $qb->add_and($this->_get_condition($field, $value));

        if($limit = $search["limit"])
            $qb->set_limit($limit["length"], $limit["from"]);

        if($order = $search["order"]) {
            $field = $this->_get_join_field($order["field"]);
            $qb->set_orderby([$field => "{$order["dir"]}"]);
        }

        if($global = $search["global"]) {
            $or = [];
            foreach ($search["all"] as $field)
                $or[] = $this->_get_condition($field, $global);
            $or = implode(" OR ",$or);
            $qb->add_and("($or)");
        }
    }

    private function _add_auth_condition(ComponentQB $qb): void
    {
        if (!$this->auth->is_root()) {
            $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");
        }

        if($this->auth->is_root()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        $user = $this->auth->get_user();
        if($this->auth->is_business_manager()) {
            $idparent = $user["id_parent"];
            $childs = $this->get_childs($idparent);
            $childs = array_column($childs,"id");
            $qb->add_in("m.id", $childs);
            $qb->add_and("m.delete_date IS NULL");
            return;
        }

        if($this->auth->is_business_owner()) {
            $childs = $this->get_childs($user["id"]);
            $childs = array_column($childs,"id");
            $childs[] = $user["id"];
            $qb->add_and("m.delete_date IS NULL");
            $qb->add_in("m.id", $childs);
        }
    }

    public function search(array $search): array
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("xxx.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                %SEARCH_FIELDS%
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
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_join("LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'")
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
