<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\AppRepository
 * @file AppRepository.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */

namespace App\Shared\Domain\Repositories;

use Exception;
use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Traits\LogTrait;
use App\Shared\Infrastructure\Exceptions\RepositoryException;
use TheFramework\Components\Db\{ComponentMysql, ComponentQB};

abstract class AppRepository
{
    use LogTrait;

    protected ?AppEntity $appEntity = null;
    protected ?ComponentMysql $componentMysql = null;
    protected string $table = "";
    //protected array $joins = []; hay un trait que rompe con esto

    protected function _getSanitizedString(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }

    protected function _getQueryBuilderInstance(): ComponentQB
    {
        return new ComponentQB;
    }

    protected function throwRepositoryException(
        string $message,
        int $code = ExceptionType::CODE_REQUESTED_RANGE_NOT_SATISFIABLE
    ): void {
        throw new RepositoryException($message, $code);
    }

    private function _getPkFieldsAndValues(array $arData): array
    {
        $pks = [];
        foreach($arData as $fieldName => $sValue) {
            if(in_array($fieldName, $this->appEntity->getPks())) {
                $pks[$fieldName] = $sValue;
            }
        }
        return $pks;
    }

    private function _getNoPkFieldsAndValues(array $arData): array
    {
        $pks = [];
        foreach($arData as $fieldName => $sValue) {
            if (!in_array($fieldName, $this->appEntity->getPks())) {
                $pks[$fieldName] = $sValue;
            }
        }
        return $pks;
    }

    private function _fieldInEntityFields(string $fieldName): bool
    {
        if (!$this->appEntity) {
            return true;
        }
        return $this->appEntity->isInFields($fieldName);
    }

    public function setAppEntity(AppEntity $appEntity): self
    {
        $this->appEntity = $appEntity;
        return $this;
    }

    public function query(?string $sql, ?int $col = null, ?int $row = null): array|string|null
    {
        try {
            $mxRet = $this->componentMysql->query($sql, $col, $row);
            return $mxRet;
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-query");
            $this->logErr($this->componentMysql, "on-query db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    public function getQueryWithCount(string $sqlCount, ?string $sql): array
    {
        try {
            return [
                "result" => $this->componentMysql->set_sqlcount($sqlCount)->query($sql),
                "total" => $this->componentMysql->get_foundrows(),
            ];
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-query-with-count");
            $this->logErr($this->componentMysql, "on-query-with-count db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    public function execute(?string $sql): int|false
    {
        try {
            return $this->componentMysql->exec($sql);
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-execute");
            $this->logErr($this->componentMysql, "on-execute db");
            $this->throwRepositoryException(__("Error persiting data"));
        }
    }

    public function insert(array $request): int
    {
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.insert")
            ->set_db($this->componentMysql)
            ->set_table($this->table);

        foreach($request as $field => $value) {
            if (!$this->_fieldInEntityFields($field)) {
                continue;
            }
            $qb->add_insert_fv($field, $value);
        }

        try {
            $qb->insert()->exec($qb::WRITE);
            return $this->componentMysql->get_lastid();
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-insert");
            $this->logErr($this->componentMysql, "on-insert db");
            $this->throwRepositoryException(__("Error persiting data"));
        }
    }//insert

    public function update(array $request): int
    {
        $pks = $this->_getPkFieldsAndValues($request);
        if (!$pks) {
            $this->throwRepositoryException(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);
        }

        $mutables = $this->_getNoPkFieldsAndValues($request);
        if (!$mutables) {
            $this->throwRepositoryException(__("No data to update"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);
        }

        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.update")
            ->set_db($this->componentMysql)
            ->set_table($this->table);

        //valores del "SET"
        foreach($mutables as $fieldName => $sValue) {
            if (!$this->_fieldInEntityFields($fieldName)) {
                continue;
            }
            $qb->add_update_fv($fieldName, $sValue);
        }

        //valores del WHERE
        foreach($pks as $fieldName => $sValue) {
            if (!$this->_fieldInEntityFields($fieldName)) {
                continue;
            }
            $qb->add_pk_fv($fieldName, $sValue);
        }

        try {
            $qb->update()->exec($qb::WRITE);
            return $this->componentMysql->get_affected();
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-update");
            $this->logErr($this->componentMysql, "on-update db");
            $this->throwRepositoryException(__("Error updating data"));
        }
    }//update

    public function delete(array $request): int
    {
        $pks = $this->_getPkFieldsAndValues($request);
        if (!$pks) {
            $this->throwRepositoryException(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);
        }

        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.delete")
            ->set_db($this->componentMysql)
            ->set_table($this->table);

        //valores del WHERE
        foreach($pks as $fieldName => $sValue) {
            if (!$this->_fieldInEntityFields($fieldName)) {
                continue;
            }
            $qb->add_pk_fv($fieldName, $sValue);
        }

        try {
            $qb->delete()->exec($qb::WRITE);
            return $this->componentMysql->get_affected();
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-delete");
            $this->logErr($this->componentMysql, "on-delete db");
            $this->throwRepositoryException(__("Error deleting data"));
        }
    }//delete

    public function getSysUpdateDateByPkFields(array $pkFieldsAndValues): string
    {
        if (!$pkFieldsAndValues) {
            return "";
        }

        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.get_sysupdate")
            ->set_db($this->componentMysql)
            ->set_getfields(["m.update_date"])
            ->set_table("$this->table as m")
        ;

        foreach($pkFieldsAndValues as $fieldName => $sValue) {
            $qb->add_pk_fv($fieldName, $sValue);
        }

        $sql = $qb->select()->sql();
        try {
            $r = $this->componentMysql->query($sql);
            $this->mapFieldsToInt($r, ["id"]);
            return $r[0]["update_date"] ?? "";
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-sysdata");
            $this->logErr($this->componentMysql, "on-sysdata db");
            $this->throwRepositoryException(__("Error reading sysdata"));
        }
    }

    public function getCsvCru(array $row, string $idUser): string
    {
        $now = date("Y-m-d H:i:s");
        $crucsv = $row["cru_csvnote"] ?? "";
        $crucsv = "delete_user:{$row["delete_user"]},delete_date:{$row["delete_date"]},delete_platform:{$row["delete_platform"]},($idUser:$now)|".$crucsv;
        return substr($crucsv, 0, 499);
    }

    public function getEntityIdByEntityUuid(string $entityUuid): int
    {
        $entityUuid = $this->_getSanitizedString($entityUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.get_id_by_uuid")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.uuid='$entityUuid'")
            ->select()->sql()
        ;
        try {
            $r = $this->componentMysql->query($sql);
            $this->mapFieldsToInt($r, ["id"]);
            return $r[0]["id"] ?? 0;
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-get-id-by-uuid");
            $this->logErr($this->componentMysql, "on-get-id-by-uuid db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    public function getEntityByEntityId(string $entityId, array $fields = []): array
    {
        $entityId = (int) $entityId;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.get_by_id")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.id=$entityId")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();
        try {
            $r = $this->componentMysql->query($sql);
            $this->mapFieldsToInt($r, ["id"]);
            return $r[0] ?? [];
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-get-by-id");
            $this->logErr($this->componentMysql, "on-get-by-id db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    public function getEntityByEntityUuid(string $entityUuid, array $fields = []): array
    {
        $entityUuid = $this->_getSanitizedString($entityUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.get_by_uuid")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.uuid='$entityUuid'")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }

        $sql = $sql->select()->sql();
        try {
            $r = $this->componentMysql->query($sql);
            //@eaftodo aqui hay que incluir el resto de ids que sean enteros
            $this->mapFieldsToInt($r, ["id"]);
            return $r[0] ?? [];
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-get-by-uuid");
            $this->logErr($this->componentMysql, "on-get-by-uuid db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    public function isDeletedByEntityId(string $entityId): bool
    {
        $entityId = (int) $entityId;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apprepository.is_deleted")
            ->set_table("$this->table as m")
            ->set_getfields(["m.delete_date"])
            ->add_and("m.id=$entityId")
            ->select()->sql()
        ;
        try {
            $r = $this->componentMysql->query($sql);
            $this->mapFieldsToInt($r, ["id"]);
            return (bool) ($r[0]["delete_date"] ?? "");
        } catch (Exception $ex) {
            $this->logErr([$ex->getMessage(), $ex->getCode(), $ex->getLine(), $ex->getFile()], "on-is-deleted");
            $this->logErr($this->componentMysql, "on-is-deleted db");
            $this->throwRepositoryException(__("Error reading data"));
        }
    }

    protected function mapFieldsToInt(array &$rows, array $fields): void
    {
        if (!$rows || !$fields) {
            return;
        }

        foreach ($rows as $i => $row) {
            foreach ($row as $field => $value) {
                if (!in_array($field, $fields)) {
                    continue;
                }
                $rows[$i][$field] = (int) $value;
            }
        }
    }

}//AppRepository
