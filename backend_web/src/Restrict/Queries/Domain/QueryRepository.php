<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Queries\Domain\QueryRepository
 * @file QueryRepository.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Queries\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class QueryRepository extends AppRepository
{
    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_query";
    }

    public function getQueryByUuidAndIdUser(string $queryUuid, int $idUser, array $fields = []): array
    {
        $queryUuid = $this->_getSanitizedString($queryUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("app_query.getQueryByUuidAndIdUser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.uuid = '$queryUuid'")
            ->add_and("m.insert_user = $idUser")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();
        $r = $this->componentMysql->query($sql);
        return $r[0] ?? [];
    }

}//QueryRepository
