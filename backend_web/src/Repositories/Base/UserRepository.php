<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\UserRepository 
 * @file UserRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Base;

use App\Services\Auth\AuthService;
use App\Components\Hierarchy\HierarchyComponent;
use App\Factories\RepositoryFactory as RF;
use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;
use TheFramework\Components\Db\ComponentQB;
use App\Factories\ComponentFactory as CF;

final class UserRepository extends AppRepository
{
    private array $joins;
    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user";
        $this->joins = [
            "fields" => [
                "u1.description"  => "e_parent",
                "u2.description"  => "e_deletedby",
                "ar1.description" => "e_language",
                "ar2.description" => "e_profile",
                "ar3.description" => "e_country",
            ],
            "on" => [
                "LEFT JOIN base_user u1 ON m.id_parent = u1.id",
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'",
                "LEFT JOIN base_array ar2 ON m.id_profile = ar2.id AND ar2.type='profile'",
                "LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'",
            ]
        ];
    }

    private function _get_join_field(string $field): string
    {
        //key: u1.description | false si no encuentra
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
        if (!$this->auth) {
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
            ->set_comment("user.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.address",
                "m.birthdate",
                "m.date_validated",
                "m.description",
                "m.email",
                "m.fullname",
                "m.id_country",
                "m.id_gender",
                "m.id_language",
                "m.id_parent",
                "m.id_profile",
                "m.is_notifiable",
                "m.secret",
                "m.phone",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id"=>"DESC"])
        ;
        $this->_add_joins($qb);
        $this->_add_search_filter($qb, $search);
        $this->_add_auth_condition($qb);

        $sql = $qb->select()->sql();
        $r = $this->db->query($sql);

        return [
            "result" => $r,
            "total" => $this->db->get_foundrows()
        ];
    }

    public function get_by_email(string $email): array
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->_get_qbuilder()
            ->set_comment("user.get_by_email")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.id", "m.fullname","m.description", "m.email", "m.secret", "m.id_language", "m.id_profile",
                "m.uuid", "m.id_parent",
                "ar1.code_erp as e_language"
            ])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.email='$email'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        if(count($r)>1 || !$r) return [];
        return $r[0];
    }

    public function email_exists(string $email): int
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->_get_qbuilder()
            ->set_comment("user.email_exists")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.email='$email'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return intval($r[0]["id"] ?? 0);
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("user.get_info(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.update_date", "m.update_user", "m.insert_date", "m.insert_user", "m.delete_date", "m.delete_user",
                "m.uuid","m.id, m.email, m.secret, m.fullname, m.address, m.birthdate",
                "m.phone",
                "m.id_profile","m.id_parent", "m.id_country", "m.id_language",

                "ar2.description as e_profile",
                "u.description as e_parent",
                "ar3.description as e_country",
                "ar1.description as e_language",
            ])
            ->add_join("LEFT JOIN base_user u ON m.id_parent = u.id")
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_join("LEFT JOIN base_array ar2 ON m.id_profile = ar2.id AND ar2.type='profile'")
            ->add_join("LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'")
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        if (!$r) return [];

        $sysdata = RF::get("Common\Sysfield")->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function get_all_hierarchy(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("get_all_hierarchy")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id", "m.id_parent"])
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return $r;
    }

    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

    public function get_owner(string $userid): array
    {
        /**
         * @var HierarchyComponent $hier
         */
        $hier = CF::get("Hierarchy\Hierarchy");
        return $hier->get_topparent($userid, $this->get_all_hierarchy());
    }

    public function get_ownerid(string $userid): int
    {
        $owner = $this->get_owner($userid);
        return (int) $owner["id"];
    }

    public function get_childs(string $userid): array
    {
        /**
         * @var HierarchyComponent $hier
         */
        $hier = CF::get("Hierarchy\Hierarchy");
        return $hier->get_childs($userid, $this->get_all_hierarchy());
    }

}//UserRepository
