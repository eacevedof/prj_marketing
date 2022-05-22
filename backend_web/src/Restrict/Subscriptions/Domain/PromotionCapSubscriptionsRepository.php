<?php
namespace App\Restrict\Subscriptions\Domain;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Restrict\Queries\Domain\Events\QueryWasCreatedEvent;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use TheFramework\Components\Db\ComponentQB;

final class PromotionCapSubscriptionsRepository extends AppRepository implements IEventDispatcher
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

    private function _dispatch(array $payload): void
    {
        EventBus::instance()->publish(...[
            QueryWasCreatedEvent::from_primitives(-1, $payload)
        ]);
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
        $this->_dispatch([
            "uuid" => $md5 = md5($sql)."-".uniqid(),
            "description" => "read:search",
            "query" => $sql,
            "total" => $r["total"],
            "module" => "promotions",
        ]);

        $r["req_uuid"] = $md5;
        return $r;
    }

    public function get_info(string $uuid, array $fields = []): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("promotiocapsusbscriptions.get_info")
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
                "m.id_promouser",
                "m.date_subscription",
                "m.date_confirm",
                "m.date_execution",
                "m.code_execution",
                "m.exec_user",
                "m.subs_status",
                "m.remote_ip",
                "m.is_test",
                "m.notes"
            ])
            ->add_and("m.uuid='$uuid'")
        ;
        if ($fields) $sql->set_getfields($fields);
        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        if (!$r) return [];

        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);

        return array_merge($r, $sysdata);
    }

    public function get_info_for_execute_date(string $uuid, array $fields = []): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("promotiocapsusbscriptions.get_info_for_execute_date")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.uuid",
                "m.id_owner",
                "m.is_test",
                "m.subs_status"
            ])
            ->add_and("m.uuid='$uuid'")
        ;
        if ($fields) $sql->set_getfields($fields);
        $this->_add_joins($sql);

        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function is_test_mode_by_id_capuser(int $idcapuser): bool
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("promotiocapsusbscriptions.is_test_mode_by_id_capuser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_promouser=$idcapuser")
            ->add_and("m.is_test=1")
        ;
        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        return (bool)($r[0]["id"] ?? 0);
    }

    public function mark_finished_by_id_promotion(int $idpromotion): void
    {
        $iduser = $this->auth->get_user()["id"] ?? -1;
        $sql = $this->_get_qbuilder()
            ->set_comment("promotiocapsusbscriptions.mark_finished_by_id_promotion")
            ->set_table($this->table)
            ->add_update_fv("update_date", date("Y-m-d H:i:s"))
            ->add_update_fv("update_user", $iduser)
            ->add_update_fv("subs_status", PromotionCapActionType::FINISHED)
            ->add_and("id_promotion=$idpromotion")
            ->add_and("delete_date IS NULL")
            ->add_and("date_execution IS NULL")
        ;
        $sql = $sql->update()->sql();
        $this->execute($sql);
    }

    public function set_auth(AuthService $auth): self
    {
        $this->auth = $auth;
        return $this;
    }
}
