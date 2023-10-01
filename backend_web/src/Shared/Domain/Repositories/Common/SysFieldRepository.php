<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\Common\SysfieldRepository
 * @file SysfieldRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Domain\Repositories\Common;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class SysFieldRepository extends AppRepository
{
    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
    }

    private function _getUserDescriptionByIdUser(string $idUser): string
    {
        if (!$idUser) {
            return "";
        }
        $id = (int) $idUser;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("sysfield._getUserDescriptionByIdUser")
            ->set_table("base_user as m")
            ->set_getfields(["m.description"])
            ->add_and("m.id=$id")
            ->select()->sql()
        ;
        $r = $this->componentMysql->query($sql);
        return $r[0]["description"] ?? "";
    }

    private function _getPlatformDescriptionByCodeErp(string $idPlatform): string
    {
        if (!$idPlatform) {
            return "";
        }
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("sysfield._getPlatformDescriptionByCodeErp")
            ->set_table("base_array as m")
            ->set_getfields(["m.description"])
            ->add_and("m.code_erp=$idPlatform")
            ->add_and("m.type='platform'")
            ->select()->sql()
        ;
        $r = $this->componentMysql->query($sql);
        return $r[0]["description"] ?? "";
    }

    public function getSysDataByRowData(array $row): array
    {
        $sys = [];
        if (isset($row["insert_user"])) {
            $sys["insert_user"] = "";
        }
        if (isset($row["insert_platform"])) {
            $sys["insert_platform"] = "";
        }
        if (isset($row["update_user"])) {
            $sys["update_user"] = "";
        }
        if (isset($row["update_platform"])) {
            $sys["update_platform"] = "";
        }
        if (isset($row["delete_user"])) {
            $sys["delete_user"] = "";
        }
        if (isset($row["delete_platform"])) {
            $sys["delete_platform"] = "";
        }
        if (isset($row["disabled_user"])) {
            $sys["disabled_user"] = "";
        }

        if (!$sys) {
            return [];
        }

        foreach ($sys as $key => $v) {
            $sys[$key] = (strstr($key, "_user"))
                ? $this->_getUserDescriptionByIdUser($row[$key])
                : $this->_getPlatformDescriptionByCodeErp($row[$key]);
        }
        return $sys;
    }
}//ExampleRepository
