<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Rw\ReaderService
 * @file ReaderService.php 1.0.0
 * @date 27-06-2019 17:55 SPAIN
 * @observations
 */
namespace App\Apify\Application\Rw;

use TheFramework\Components\Db\Context\ComponentContext;
use TheFramework\Components\Db\ComponentQB;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;
use App\Shared\Infrastructure\Traits\CacheQueryTrait;

final class ReaderService extends AppService
{
    use CacheQueryTrait;

    private $idcontext;
    private $dbname;
    
    private $oContext;
    private $behaveschema;
    private string $sql;
    private int $foundrows;
    private float $cachettl = 0;
    private string $maintable = "";

    public function __construct(string $idcontext="", string $dbalias="")
    {
        $this->idcontext = $idcontext;

        if(!$this->idcontext) return $this->add_error("Error in context: $idcontext");
        $this->oContext = new ComponentContext($_ENV["APP_CONTEXTS"], $idcontext);
        $this->dbname = $this->oContext->get_dbname($dbalias);
        $oDb = DbFactory::get_dbobject_by_ctx($this->oContext, $this->dbname);
        if($oDb->is_error()) return $this->add_error($oDb->get_errors());

        $this->behaveschema = new SchemaBehaviour($oDb);
    }
    
    private function _get_parsed_tosql(array $qparams): string
    {
        if(!isset($qparams["fields"]) || !is_array($qparams["fields"]))
            $this->_exception("invalid or empty fields in read params");

        $qb = new ComponentQB();
        if($qparams["comment"] ?? "") $qb->set_comment($qparams["comment"]);

        $qb->set_table($qparams["table"]);
        if(isset($qparams["distinct"])) $qb->distinct((bool) $qparams["distinct"]);
        if(isset($qparams["foundrows"])) $qb->calcfoundrows((bool) $qparams["foundrows"]);

        $qb->set_getfields($qparams["fields"]);
        $qb->set_joins($qparams["joins"] ?? []);
        $qb->set_and($qparams["where"] ?? []);
        $qb->set_groupby($qparams["groupby"] ?? []);
        $qb->set_having($qparams["having"] ?? []);

        if(isset($qparams["orderby"]))
        {
            foreach($qparams["orderby"] as $sField) {
                $fields = explode(" ",trim($sField));
                $qb->add_orderby($fields[0], $fields[1] ?? "ASC");
            }
        }

        if(isset($qparams["limit"]["perpage"]))
            $qb->set_limit($qparams["limit"]["perpage"] ?? 1000,$qparams["limit"]["regfrom"]??0);

        $qb->select();
        return $qb->sql();
    }

    public function read_raw(string $sql): array
    {
        //intento leer cache
        if($ttl = $this->cachettl)
        {
            if($r = $this->get_cached($sql, $this->maintable)) {
                $this->foundrows = $this->get_cachedcount($sql, $this->maintable);
                return $r;
            }
        }

        //leo de bd
        $r = $this->behaveschema->read_raw($sql);
        $this->foundrows = $this->behaveschema->get_foundrows();
        if($this->behaveschema->is_error()) {
            if($ttl) $this->cache_del_qandcount($sql, $this->maintable);
            $this->logerr($errors = $this->behaveschema->get_errors(),"readservice.read_raw");
            $this->add_error($errors);
            return $r;
        }

        //la consulta ha ido bien. guardo en cache
        if($ttl) {
            $this->addto_cache($sql, $r, $ttl, $this->maintable);
            $this->addto_cachecount($sql, $this->foundrows, $ttl, $this->maintable);
        }
        return $r;
    }
    
    public function get_read(array $qparams): array
    {
        if(!is_array($qparams)) $this->_exception("read params is not an array");
        if(!$table = trim($qparams["table"])) $this->_exception("missing read table");

        $this->maintable = explode(" ", $table)[0];
        $this->cachettl = (int) $qparams["cache_time"] ?? 0;
        $sql = $this->_get_parsed_tosql($qparams);
        $r = $this->read_raw($sql);
        return $r;
    }

    public function get_foundrows(){return $this->foundrows;}

}//ReaderService
