<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Queries\Domain\Query_actionsRepository
 * @file Query_actionsRepository.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Queries\Domain;

use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Shared\Infrastructure\Factories\{DbFactory as DbF, RepositoryFactory as RF};

final class QueryActionsRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_query_actions";
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

    public function search(array $search): array
    {
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("query_actions.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.id_query",
                "m.description",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id" => "DESC"])
        ;
        $this->_addJoinsToQueryBuilder($qb);
        $this->_addSearchFilterToQueryBuilder($qb, $search);
        $this->_addConditionByAuthService($qb);

        $sql = $qb->select()->sql();
        $sqlCount = $qb->sqlcount();
        $r = $this->componentMysql->set_sqlcount($sqlCount)->query($sql);

        return [
            "result" => $r,
            "total" => $this->componentMysql->get_foundrows()
        ];
    }

    public function getQueryInfoByQueryUuid(string $queryUuid): array
    {
        $queryUuid = $this->_getSanitizedString($queryUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("query_actions.get_info(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.insert_user",
                "m.insert_date",
                "m.id",
                "m.id_query",
                "m.description"
            ])
            //->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.uuid='$queryUuid'")
            ->select()->sql()
        ;
        $r = $this->componentMysql->query($sql);
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

}//Query_actionsRepository
