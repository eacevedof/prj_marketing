<?php

namespace App\Restrict\Subscriptions\Domain;

use App\Shared\Infrastructure\Bus\EventBus;
use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Restrict\Queries\Domain\Events\QueryWasCreatedEvent;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Shared\Infrastructure\Factories\{DbFactory as DbF, RepositoryFactory as RF};

final class PromotionCapSubscriptionsRepository extends AppRepository implements IEventDispatcher
{
    //calculatedFields, joins
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_promotioncap_subscriptions";
        $this->calculatedFields = [
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
                "p.uuid" => "e_promocode",
                "bd.business_name" => "e_business",
                "ar1.description" => "e_status",
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

    private function _addConditionByAuthService(ComponentQB $qb): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $qb->add_and("1 = 0");
            return;
        }

        if ($this->authService->isAuthUserRoot()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        //como no es root no puede ver borrados
        $qb->add_and("m.delete_date IS NULL");

        $authUser = $this->authService->getAuthUserArray();
        if ($this->authService->isAuthUserBusinessOwner()) {
            $qb->add_andoper("m.id_owner", $authUser["id"]);
            return;
        }

        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $authUser["id_parent"];
            $qb->add_andoper("m.id_owner", $idParent);
        }
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            QueryWasCreatedEvent::fromPrimitives(-1, $payload)
        ]);
    }

    public function search(array $search): array
    {
        $qb = $this->_getQueryBuilderInstance()
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
                "m.date_execution" => "DESC",
                "m.date_confirm" => "DESC",
                "m.date_subscription" => "DESC"
            ])
        ;

        $this->_addCalculatedFieldToQueryBuilder($qb);
        $this->_addJoinsToQueryBuilder($qb);
        $this->_addSearchFilterToQueryBuilder($qb, $search);
        $this->_addConditionByAuthService($qb);

        $sql = $qb->select()->sql();
        $sqlCount = $qb->sqlcount();
        $r = $this->getQueryWithCount($sqlCount, $sql);
        $this->_dispatchEvents([
            "uuid" => $md5 = md5($sql)."-".uniqid(),
            "description" => "read:search",
            "query" => $sql,
            "total" => $r["total"],
            "module" => "subscriptions",
        ]);

        $r["req_uuid"] = $md5;
        return $r;
    }

    public function getCapSubscriptionInfoBySubscriptionUuid(string $subscriptionUuid, array $fields = []): array
    {
        $subscriptionUuid = $this->_getSanitizedString($subscriptionUuid);
        $sql = $this->_getQueryBuilderInstance()
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
            ->add_and("m.uuid='$subscriptionUuid'")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        if (!$r) {
            return [];
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);
        return array_merge($r, $sysData);
    }

    public function getCapSubscriptionInfoForExecuteDate(string $subscriptionUuid, array $fields = []): array
    {
        $subscriptionUuid = $this->_getSanitizedString($subscriptionUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotiocapsusbscriptions.get_info_for_execute_date")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.uuid",
                "m.id_owner",
                "m.is_test",
                "m.subs_status"
            ])
            ->add_and("m.uuid='$subscriptionUuid'")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $this->_addJoinsToQueryBuilder($sql);

        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        return $r[0] ?? [];
    }

    public function isTestModeByIdCapUser(int $idCapUser): bool
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotiocapsusbscriptions.isTestModeByIdCapUser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_promouser = $idCapUser")
            ->add_and("m.is_test=1")
        ;
        $sql = $sql->select()->sql();
        $r = $this->query($sql);
        return (bool) ($r[0]["id"] ?? 0);
    }

    public function markCapSubscriptionAsFinished(int $idPromotion): void
    {
        $idUser = $this->authService->getAuthUserArray()["id"] ?? -1;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotiocapsusbscriptions.mark_finished_by_id_promotion")
            ->set_table($this->table)
            ->add_update_fv("update_date", date("Y-m-d H:i:s"))
            ->add_update_fv("update_user", $idUser)
            ->add_update_fv("subs_status", PromotionCapActionType::FINISHED)
            ->add_and("id_promotion = $idPromotion")
            ->add_and("delete_date IS NULL")
            ->add_and("date_execution IS NULL")
        ;
        $sql = $sql->update()->sql();
        $this->execute($sql);
    }

    public function setAuthService(AuthService $authService): self
    {
        $this->authService = $authService;
        return $this;
    }
}
