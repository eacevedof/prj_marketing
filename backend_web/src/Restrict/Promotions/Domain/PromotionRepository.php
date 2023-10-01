<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionRepository
 * @file PromotionRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 */

namespace App\Restrict\Promotions\Domain;

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

final class PromotionRepository extends AppRepository implements IEventDispatcher
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_promotion";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
                "u3.description"  => "e_owner",
                "ar1.description" => "e_is_published",
                "bd.slug" => "e_business_slug"
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "LEFT JOIN base_user u3 ON m.id_owner = u3.id",
                "LEFT JOIN app_array ar1 ON m.is_published = ar1.id_pk AND ar1.type='bool'",
                "LEFT JOIN app_array ar2 ON m.id_tz = ar2.id_pk AND ar2.type='tz'",
                "LEFT JOIN app_business_data bd ON m.id_owner = bd.id_user",
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

        //como no es root no puede ver borrados o desactivados
        $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");

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
            ->set_comment("promotion.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.slug",
                "m.date_from",
                "m.date_to",
                "m.content",
                /*
                "m.bgcolor",
                "m.bgimage_xs",
                "m.bgimage_sm",
                "m.bgimage_md",
                "m.bgimage_lg",
                "m.bgimage_xl",
                "m.bgimage_xxl",
                */
                "m.is_published",
                "m.invested",
                "m.returned",
                "m.max_confirmed",
                "m.notes",
                "m.delete_date",
                "m.disabled_date",
                "m.num_confirmed",
                "m.num_executed",
            ])
            ->set_limit(25)
            ->set_orderby(["m.id" => "DESC"])
        ;
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
            "module" => "promotions",
        ]);

        $r["req_uuid"] = $md5;
        return $r;
    }

    public function getPromotionInfoByPromotionUuid(string $promotionUuid): array
    {
        $promotionUuid = $this->_getSanitizedString($promotionUuid);
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("promotion.getPromotionInfoByPromotionUuid")
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
                "m.id_tz",
                "m.code_erp",
                "m.description",
                "m.slug",
                "m.date_from",
                "m.date_to",
                "m.date_execution",
                "m.content",
                "m.bgcolor",
                "m.bgimage_xs",
                "m.bgimage_sm",
                "m.bgimage_md",
                "m.bgimage_lg",
                "m.bgimage_xl",
                "m.bgimage_xxl",
                "m.invested",
                "m.returned",
                "m.max_confirmed",
                "m.is_raffleable",
                "m.is_cumulative",
                "m.is_published",
                "m.is_launched",
                "m.tags",
                "m.notes",
                "m.num_viewed",
                "m.num_subscribed",
                "m.num_confirmed",
                "m.num_executed",
                "m.disabled_date",
                "m.disabled_reason",
                "m.disabled_user",
            ])
            ->add_and("m.uuid='$promotionUuid'")
        ;
        $this->_addJoinsToQueryBuilder($qb);
        $sql = $qb->select()->sql();
        $r = $this->query($sql);
        if (!$r) {
            return [];
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);
        return array_merge($r, $sysData);
    }

    public function setAuthService(AuthService $authService): self
    {
        $this->authService = $authService;
        return $this;
    }

    public function isPromotionLaunchedByPromotionUuid(string $promotionUuid): bool
    {
        $promotionUuid = $this->_getSanitizedString($promotionUuid);
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("promotion.isPromotionLaunchedByPromotionUuid")
            ->set_table("$this->table as m")
            ->set_getfields(["m.is_launched",])
            ->add_and("m.uuid='$promotionUuid'");
        $sql = $qb->select()->sql();
        $r = $this->query($sql);
        return (bool) $r[0]["is_launched"];
    }

    public function doesPromotionHaveSubscribersByPromotionUuid(string $promotionUuid): bool
    {
        $promotionUuid = $this->_getSanitizedString($promotionUuid);
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("promotion.doesPromotionHaveSubscribersByPromotionUuid")
            ->set_table("$this->table as m")
            ->set_getfields(["m.num_subscribed",])
            ->add_and("m.uuid='$promotionUuid'");
        $sql = $qb->select()->sql();
        $r = $this->query($sql);
        return (bool) $r[0]["num_subscribed"];
    }

    public function updatePromotionSlugWithPromotionId(int $idPromotion): void
    {
        $sql = "
        -- updatePromotionSlugWithPromotionId
        UPDATE $this->table SET slug = CONCAT(slug,'-', id) WHERE id = $idPromotion
        ";
        $this->execute($sql);
    }

    public function getPromotionByPromotionSlug(string $promotionSlug, array $fields = []): array
    {
        $promotionSlug = $this->_getSanitizedString($promotionSlug);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotion.getPromotionByPromotionSlug")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.slug='$promotionSlug'")
            ->set_limit(1)
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();
        return $this->query($sql)[0] ?? [];
    }

    public function increaseViewedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- increaseViewedByPromotionId
        UPDATE {$this->table} SET num_viewed = num_viewed + 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function increaseSubscribedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- increaseSubscribedByPromotionId
        UPDATE {$this->table} SET num_subscribed=num_subscribed + 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function increaseConfirmedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- increaseConfirmedByPromotionId
        UPDATE {$this->table} SET num_confirmed=num_confirmed + 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function increaseExecutedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- increaseExecutedByPromotionId
        UPDATE {$this->table} SET num_executed=num_executed + 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function decreaseSubscribedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- decreaseSubscribedByPromotionId
        UPDATE {$this->table} SET num_subscribed=num_subscribed - 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function decreaseConfirmedByPromotionId(int $promotionId): void
    {
        $sql = "
        -- decreaseConfirmedByPromotionId
        UPDATE {$this->table} SET num_confirmed=num_confirmed - 1 WHERE 1 AND id = {$promotionId}
        ";
        $this->execute($sql);
    }

    public function getPromotionCapStatisticsByPromotionUuid(string $promotionUuid): array
    {
        if (!$id = $this->getEntityIdByEntityUuid($promotionUuid)) {
            return [];
        }

        list($v, $s, $c, $e) = PromotionCapActionType::getAllPromotionCapTypes();

        $sql = "
        -- getStatistics
        SELECT COUNT(id) n, 'viewed'
        FROM app_promotioncap_actions pa
        WHERE 1
        AND id_type = $v
        AND id_promotion = $id
        -- AND url_req NOT LIKE '%mode=test%'
        
        UNION
        
        SELECT COUNT(id), 'subscribed'
        FROM app_promotioncap_actions pa
        WHERE 1
        AND id_type = $s
        AND id_promotion = $id
        
        UNION 
        
        SELECT COUNT(id), 'confirmed'
        FROM app_promotioncap_actions pa
        WHERE 1
        AND id_type = $c
        AND id_promotion = $id
        
        UNION 
        
        SELECT COUNT(id), 'executed'
        FROM app_promotioncap_actions pa
        WHERE 1
        AND id_type = $e
        AND id_promotion = $id
        ";
        return $this->query($sql);
    }

}
