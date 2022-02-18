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
use App\Shared\Infrastructure\Traits\LogTrait;
use App\Shared\Domain\Entities\AppEntity;
use TheFramework\Components\Db\ComponentQB;
use TheFramework\Components\Db\ComponentMysql;
use App\Shared\Domain\Enums\ExceptionType;
use \Exception;

abstract class AppRepository
{
    use LogTrait;

    protected AppEntity $entity;
    protected ComponentMysql $db;
    protected string $table;

    protected function _get_sanitized(string $value): string
    {
        return str_replace("'","\\'", $value);
    }

    protected function _get_qbuilder(): ComponentQB
    {
        return new ComponentQB();
    }

    protected function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"apprepository.exception");
        throw new Exception($message, $code);
    }

    private function _get_pks($arData)
    {
        $pks = [];
        foreach($arData as $fieldname=>$sValue)
            if(in_array($fieldname,$this->entity->get_pks()))
                $pks[$fieldname] = $sValue;
        return $pks;
    }

    private function _get_no_pks($arData)
    {
        $pks = [];
        foreach($arData as $fieldname=>$sValue)
            if(!in_array($fieldname, $this->entity->get_pks()))
                $pks[$fieldname] = $sValue;
        return $pks;
    }

    private function _is_in_entity(string $fieldname): bool
    {
        if (!$this->entity) return true;
        return $this->entity->in_fields($fieldname);
    }

    public function set_model(AppEntity $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function query(?string $sql, ?int $col=null, ?int $row=null)
    {
        $mxRet = $this->db->query($sql, $col, $row);
        return $mxRet;
    }

    public function execute(?string $sql)
    {
        $mxRet = $this->db->exec($sql);
        return $mxRet;
    }

    public function insert(array $request): int
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("apprepository.insert(request)")
            ->set_db($this->db)
            ->set_table($this->table);

        foreach($request as $field => $value) {
            if (!$this->_is_in_entity($field)) continue;
            $qb->add_insert_fv($field, $value);
        }

        $qb->insert()->exec($qb::WRITE);
        return $this->db->get_lastid();
    }//insert

    public function update(array $request): int
    {
        $pks = $this->_get_pks($request);
        if(!$pks) $this->_exception(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $mutables = $this->_get_no_pks($request);
        if(!$mutables)
            $this->_exception(__("No data to update"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $qb = $this->_get_qbuilder()
            ->set_comment("apprepository.update(request)")
            ->set_db($this->db)
            ->set_table($this->table);

        //valores del "SET"
        foreach($mutables as $fieldname=>$sValue) {
            if (!$this->_is_in_entity($fieldname)) continue;
            $qb->add_update_fv($fieldname, $sValue);
        }

        //valores del WHERE
        foreach($pks as $fieldname=>$sValue) {
            if (!$this->_is_in_entity($fieldname)) continue;
            $qb->add_pk_fv($fieldname, $sValue);
        }

        $qb->update()->exec($qb::WRITE);
        return $this->db->get_affected();
    }//update

    public function delete(array $request): int
    {
        $pks = $this->_get_pks($request);
        if(!$pks) $this->_exception(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $qb = $this->_get_qbuilder()
            ->set_comment("apprepository.delete(request)")
            ->set_db($this->db)
            ->set_table($this->table);

        //valores del WHERE
        foreach($pks as $fieldname=>$sValue) {
            if (!$this->_is_in_entity($fieldname)) continue;
            $qb->add_pk_fv($fieldname, $sValue);
        }

        $qb->delete()->exec($qb::WRITE);
        return $this->db->get_affected();
    }//delete

    public function get_sysupdate(array $pks): string
    {
        if(!$pks) return "";

        $qb = $this->_get_qbuilder()
            ->set_comment("app.sysupdate")
            ->set_db($this->db)
            ->set_getfields(["m.update_date"])
            ->set_table("$this->table as m")
        ;

        foreach($pks as $fieldname=>$sValue)
            $qb->add_pk_fv($fieldname, $sValue);

        $sql = $qb->select()->sql();
        $r = $this->db->query($sql);
        return $r[0]["update_date"] ?? "";
    }

    public function get_csvcru(array $row, string $iduser): string
    {
        $now = date("Y-m-d H:i:s");
        $crucsv = $row["cru_csvnote"] ?? "";
        $crucsv = "delete_user:{$row["delete_user"]},delete_date:{$row["delete_date"]},delete_platform:{$row["delete_platform"]},($iduser:$now)|".$crucsv;
        return substr($crucsv,0,499);
    }

    public function get_id_by_uuid(string $uuid): int
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("apprepository.get_id_by(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return intval($r[0]["id"] ?? 0);
    }

    public function get_by_id(string $id): array
    {
        $id = (int) $id;
        $sql = $this->_get_qbuilder()
            ->set_comment("get_by_id")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.id=$id")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return $r[0] ?? [];
    }

    public function is_deleted(string $id): bool
    {
        $id = (int) $id;
        $sql = $this->_get_qbuilder()
            ->set_comment("get_by_id")
            ->set_table("$this->table as m")
            ->set_getfields(["m.delete_date"])
            ->add_and("m.id=$id")
            ->select()->sql()
        ;
        $r = $this->db->query($sql);
        return (bool) ($r[0]["delete_date"] ?? "");
    }

}//AppRepository
