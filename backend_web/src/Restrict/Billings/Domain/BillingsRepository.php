<?php
namespace App\Restrict\Billings\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use TheFramework\Components\Db\ComponentQB;

final class BillingsRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "app_promotioncap_subscriptions";
        $this->calcfields = [
            "CASE m.is_test WHEN 0 THEN 'No' ELSE 'Yes' END" => "c_is_test"
        ];
        $this->joins = [
            "fields" => [
                "u1.description" => "e_owner",
                "u2.description"  => "e_deletedby",
                "pu.uuid" => "e_usercode",
                "pu.name1" => "e_username",
                "pu.email" => "e_email",
                "p.description" => "e_promotion",
                "p.uuid"=>"e_promocode",
                "bd.business_name"=>"e_business",
                "ar1.description"=>"e_status",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "INNER JOIN app_promotioncap_users pu ON m.id_promouser = pu.id AND m.id_promotion = pu.id_promotion",
                "INNER JOIN app_promotion p ON m.id_promotion = p.id AND pu.id_promotion = p.id",
                "LEFT JOIN base_user u1 ON p.id_owner = u1.id",
                "INNER JOIN app_business_data bd ON p.id_owner = bd.id_user",
                "LEFT JOIN app_array ar1 ON m.subs_status = ar1.id_pk AND ar1.type='subs_status'"
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

        //como no es root no puede ver borrados
        $qb->add_and("m.delete_date IS NULL");

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
            ->set_comment("promotiocapsusbscriptions.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.id_promotion",
                "m.id_promouser",
                "m.date_subscription",
                "m.date_confirm",
                "m.date_execution",
                "m.code_execution",
                "m.exec_user",
                "m.subs_status",
                "m.remote_ip",
                "m.is_test",
                "m.notes",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby([
                "m.date_execution"=>"DESC",
                "m.date_confirm"=>"DESC",
                "m.date_subscription"=>"DESC"
            ])
        ;
        $this->_add_calcfields($qb);
        $this->_add_joins($qb);
        $this->_add_search_filter($qb, $search);
        $this->_add_auth_condition($qb);

        $sql = $qb->select()->sql();
        $sqlcount = $qb->sqlcount();
        $r = $this->query_with_count($sqlcount, $sql);
        return $r;
    }


    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }
}
