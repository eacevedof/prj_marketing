<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\ExampleRepository 
 * @file ExampleRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Base;

use App\Factories\RepositoryFactory as RF;
use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;
use TheFramework\Components\Db\ComponentCrud;

final class UserRepository extends AppRepository
{
    private array $joins;
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user";
        $this->joins = [
            "ar1.description"=>"e_language",
            "ar2.description"=>"e_profile",
            "ar3.description"=>"e_country"
        ];
    }

    public function get_by_email(string $email): array
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->_get_crud()
                ->set_table("$this->table as m")
                ->set_getfields([
                    "m.id","m.email","m.secret","m.id_language", "m.id_profile",
                    "m.uuid",
                    "ar.code_erp as language"
                ])
                ->add_join("LEFT JOIN app_array ar ON m.id_language = ar.id AND ar.type='language'")
                ->add_and("m.is_enabled=1")
                ->add_and("m.delete_date IS NULL")
                ->add_and("m.email='$email'")
                ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        if(count($r)>1 || !$r) return [];
        return $r[0];
    }

    public function email_exists(string $email): int
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->_get_crud()
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.email='$email'")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        return intval($r[0]["id"] ?? 0);
    }

    public function get_id_by(string $uuid): int
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_crud()
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.uuid='$uuid'")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        return intval($r[0]["id"] ?? 0);
    }

    public function get_by_id(string $id): int
    {
        $id = (int) $id;
        $sql = $this->_get_crud()
            ->set_table("$this->table as m")
            ->set_getfields(["*"])
            ->add_and("m.id=$id")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        return $r[0] ?? [];
    }

    private function _get_condition(string $field, string $value): string
    {
        $jfield = array_search($field, $this->joins);
        if ($jfield===false)
            return "m.$field LIKE '%$value%'";

        return "$jfield LIKE '%$value%'";
    }

    private function _add_search(ComponentCrud $crud, array $search): void
    {
        if(!$search) return;

        if($fields = $search["fields"])
            foreach ($fields as $field => $value )
                $crud->add_and($this->_get_condition($field, $value));

        if($limit = $search["limit"])
            $crud->set_limit($limit["length"], $limit["from"]);

        if($order = $search["order"])
            $crud->set_orderby(["m.{$order["field"]}"=>"{$order["dir"]}"]);

        if($global = $search["global"]) {
            $or = [];
            foreach ($search["all"] as $field)
                $or[] = "m.$field LIKE '%$global%'";
            $or = implode(" OR ",$or);
            $crud->add_and("($or)");
        }
    }

    private function _add_joins(ComponentCrud $crud): void
    {
        foreach ($this->joins as $field => $alias)
            $crud->add_getfield("$field as $alias");

        $crud
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_join("LEFT JOIN base_array ar2 ON m.id_profile = ar2.id AND ar2.type='profile'")
            ->add_join("LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'");
    }

    public function search(array $search): array
    {
        $crud = $this->_get_crud()
            ->set_table("$this->table as m")
            ->is_foundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.address",
                "m.birthdate",
                "m.date_validated",
                "m.delete_date",
                "m.delete_platform",
                "m.delete_user",
                "m.description",
                "m.email",
                "m.fullname",
                "m.id_country",
                "m.id_gender",
                "m.id_language",
                "m.id_nationality",
                "m.id_parent",
                "m.id_profile",
                "m.insert_date",
                "m.insert_platform",
                "m.insert_user",
                "m.is_notificable",
                "m.secret",
                "m.phone",
            ])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->set_limit(25, 0)
            ->set_orderby(["m.id"=>"DESC"])
        ;
        $this->_add_joins($crud);
        $this->_add_search($crud, $search);

        $sql = $crud->get_selectfrom();
        return [
            "result" => $this->db->query($sql),
            "total" => $this->db->get_foundrows()
        ];
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_crud()
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.update_date", "m.update_user", "m.insert_date", "m.insert_user",
                "m.uuid","m.id, m.email, m.secret, m.fullname, m.address, m.birthdate",
                "m.phone",
                //"m.id_profile", "m.id_language","m.id_parent",
                "u.description as e_parent",
                "ar1.description as e_language",
                "ar2.description as e_profile",
                "ar3.description as e_country",
            ])
            ->add_join("LEFT JOIN base_user u ON m.id_parent = u.id")
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_join("LEFT JOIN base_array ar2 ON m.id_profile = ar2.id AND ar2.type='profile'")
            ->add_join("LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'")
            ->add_and("m.uuid='$uuid'")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        if (!$r) return [];

        $sysdata = RF::get("Common\Sysfield")->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }


}//UserRepository
