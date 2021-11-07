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

use App\Models\Base\UserModel;
use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;
use App\Factories\ModelFactory as MF;
use TheFramework\Components\Db\ComponentCrud;

final class UserRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user";
        $this->_load_crud();
    }

    public function get_by_email(string $email): array
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->crud
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
        $ar = $this->db->query($sql);
        if(count($ar)>1) return [];
        return $ar[0] ?? [];
    }

    private function _add_search(ComponentCrud $crud, array $search): void
    {
        if(!$search) return;

        if($fields = $search["fields"])
            foreach ($fields as $field => $value )
                $crud->add_and("m.$field LIKE '%$value%'");

        if($limit = $search["limit"])
            $crud->set_limit($limit["length"], $limit["from"]);

        if($order = $search["order"])
            $crud->set_orderby("m.{$order["field"]} {$order["dir"]}");

        if($global = $search["global"]) {
            $or = [];
            foreach ($search["all"] as $field)
                $or[] = "m.$field LIKE '%$global%'";
            $or = implode(" OR ",$or);
            $crud->add_and("($or)");
        }


    }

    public function search(array $search): array
    {
        $crud = $this->crud
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

                "ar.description as language",
            ])
            ->add_join("LEFT JOIN app_array ar ON m.id_language = ar.id AND ar.type='language'")
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->set_limit(25, 0)
            ->set_orderby("m.id DESC")
        ;

        $this->_add_search($crud, $search);

        $sql = $crud->get_selectfrom();
        return [
            "result" => $this->db->query($sql),
            "total" => $this->db->get_foundrows()
        ];
    }

}//ExampleRepository
