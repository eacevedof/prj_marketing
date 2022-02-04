<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Behaviours\SchemaBehaviour
 * @file SchemaBehaviour.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Domain\Behaviours;

use App\Shared\Infrastructure\Traits\CacheQueryTrait;
use App\Shared\Infrastructure\Traits\ErrorTrait;
use App\Dbs\Application\CoreQueriesService;
use TheFramework\Components\Db\ComponentMysql;
use \Exception;

final class SchemaBehaviour
{
    use ErrorTrait;
    use CacheQueryTrait;

    private ?ComponentMysql $db;
    private CoreQueriesService $servqueries;
    private int $foundrows = 0;
    private bool $usecache = false;
    private const CACHE_TIME = 3600;
    
    public function __construct(?Object $db=null)
    {
        $this->db = $db ?? new ComponentMysql();
        $this->servqueries = new CoreQueriesService();
    }

    private function _get_orderby_pos(string $sql): ?int
    {
        $find = [" ORDER BY ", " ORDER BY\n", "\nORDER BY\n", "ORDER BY\n", "\nORDER BY ", "\nORDER BY"];
        foreach ($find as $orderby){
            $pos = strrpos($sql, $orderby, -1);
            if ($pos !== false)
                return $pos;
        }
        return null;
    }

    private function _get_limit(string $sql): ?int
    {
        $find = [" LIMIT ", " LIMIT\n", "\nLIMIT\n", "LIMIT\n", "\nLIMIT ", "\nLIMIT"];
        foreach ($find as $limit){
            $pos = strrpos($sql, $limit, -1);
            if ($pos !== false)
                return $pos;
        }
        return null;
    }

    private function _get_count_query(string $sql): string
    {
        if ($to = $this->_get_orderby_pos($sql))
            $sql = substr($sql,0, $to);

        if ($to = $this->_get_limit($sql))
            $sql = substr($sql, 0, $to);

        return "/*count-query*/SELECT COUNT(*) FROM ($sql) AS c";
    }

    public function query(string $sql, ?int $icol=null, ?int $irow=null): array
    {
        try {
            $sqlcount = $this->_get_count_query($sql);
            $r = $this->db
                ->set_sqlcount($sqlcount)
                ->query($sql, $icol, $irow)
            ;
            //to-do esto va a fallar pq ya no se usa calcrows
            $this->foundrows = $this->db->get_foundrows();
            return $r;
        }
        catch (Exception $e) {
            $this->add_error($e->getMessage());
        }
        return [];
    }

    public function execute(string $sql)
    {
        try {
            return $this->db->exec($sql);
        }
        catch (Exception $e) {
            $this->add_error($e->getMessage());
        }
    }    
    
    public function get_schemas(): array
    {
        $sql = "-- get_schemas
        SELECT schema_name as dbname
        FROM information_schema.schemata
        ORDER BY schema_name;";
        if (!$this->usecache) return $this->query($sql);

        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;
    }
    
    public function get_tables(string $dbname=""): array
    {
        $sql = $this->servqueries->get_tables($dbname);
        if (!$this->usecache) return $this->query($sql);

        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;
    }
    
    public function get_table(string $table, string $dbname=""): array
    {
        $sql = $this->servqueries->get_tables($dbname,$table);
        if (!$this->usecache) return $this->query($sql);
        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql,0);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;        
    }
   
    public function get_fields_info(string $table, string $dbname=""): array
    {
        $sql = $this->servqueries->get_fields($dbname,$table);
        if (!$this->usecache) return $this->query($sql);
        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function get_fields(string $table, string $dbname=""): array
    {
        $sql = $this->servqueries->get_fields_min($dbname,$table);
        if (!$this->usecache) return $this->query($sql);
        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function get_field_info(string $field, string $table, string $dbname=""): array
    {
        $sql = $this->servqueries->get_field($dbname, $table, $field);
        if (!$this->usecache) return $this->query($sql);
        if($r = $this->get_cached($sql)) return $r;
        $r = $this->query($sql);
        $this->addto_cache($sql, $r, self::CACHE_TIME);
        return $r;
    }    

    public function usecache(bool $use=true): self
    {
        $this->usecache = $use;
        return $this;
    }

    public function read_raw(string $sql){ return $this->query($sql);}
    public function write_raw(string $sql){ return $this->execute($sql);}
    public function get_foundrows(){return $this->foundrows; }


}//SchemaBehaviour
