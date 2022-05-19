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
        $this->table = "app_promotion";
        $this->joins = [
            "fields" => [
                "u1.description" => "e_owner",
                "bd.business_name"=>"e_business",
                "bd.slug" => "e_business_slug",

                //"billing.num_executed" => "e_num_executed",
                "billing.returned" => "e_returned",
                "billing.earned" => "e_earned",
                "billing.percent" => "e_percent",
                "billing.rate" => "e_rate",
                "billing.commission" => "e_commission",
                "billing.invested" => "e_invested",
                "billing.b_earnings" => "e_b_earnings",
            ],
            "on" => [
                "LEFT JOIN base_user u1 ON m.id_owner = u1.id", //sacar el owner
                "INNER JOIN app_business_data bd ON m.id_owner = bd.id_user",
                "INNER JOIN (
                    SELECT id  AS id_promotion, 
                    num_executed, 
                    ROUND(returned,2) returned, 
                    ROUND(earned,2) earned, 
                    ROUND(percent,2) percent,
                    ROUND(percent/100,2) rate,
                    ROUND(earned * (percent/100),2) commission,
                    ROUND(invested, 2) invested,
                    ROUND(earned - ROUND(earned * (percent/100),2) - invested,2) b_earnings
                    FROM
                    (
                        SELECT id, invested, returned, num_executed
                        , returned * num_executed earned
                        , CASE 
                            WHEN returned>0 AND returned<10 THEN 9
                            WHEN returned>=10 AND returned<20 THEN 7
                            WHEN returned>=20 AND returned<30 THEN 6
                            WHEN returned>=30 AND returned<50 THEN 3.75
                            -- WHEN returned>=50 THEN 3
                            ELSE 3 
                        END AS percent
                        FROM app_promotion p
                        WHERE 1
                        AND is_enabled=1
                        AND delete_date IS NULL
                    ) AS calc
                ) billing
                ON m.id = billing.id_promotion
                "
            ]
        ];
    }

    private function _add_auth_condition(ComponentQB $qb): void
    {
        if (!$this->auth->get_user()) {
            $qb->add_and("1 = 0");
            return;
        }

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
            ->set_comment("billings.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.slug",
                "m.num_executed",
            ])
            ->set_limit(25, 0)
        ;
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
