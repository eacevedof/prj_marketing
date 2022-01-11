<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheFramework\Components\Db\ComponentMysql
 * @file component_mysql.php v2.3.0
 * @date 10-01-2022 21:00 SPAIN
 * @observations
 */
namespace TheFramework\Components\Db;
use \PDO;
use \PDOException;
use \Exception;

final class ComponentMysql
{
    private array $config = [];

    private int $affected = 0;
    private int $foundrows = 0;
    private int $lastid = -1;

    private bool $iserror = false;
    private array $errors = [];

    public function __construct(array $config=[])
    {
        $this->config = $config;
    }

    private function _exception(string $message, int $code=500): void
    {
        throw new Exception($message, $code);
    }

    private function _get_conn_string(): string
    {
        if(!$this->config) $this->_exception("empty connection config");

        $config["mysql:host"]   = $this->config["server"] ?? "";
        $config["dbname"]       = $this->config["database"] ?? "";
        $config["port"]         = $this->config["port"] ?? "";
        //$config["ConnectionPooling"] = (isset($this->config["pool"])?$this->config["pool"]:"0");

        $strcon = "";
        foreach($config as $sK=>$sV)
            $strcon .= "$sK=$sV;";

        return $strcon;
    }//_get_conn_string

    private function _get_rowcol(array $result, ?int $col=null, ?int $row=null)
    {
        if ((is_null($col) && is_null($row)) || !$result)
            return $result;
        
        $row0 = $result[0] ?? [];
        $fieldnames = array_flip(array_keys($row0));
        $colname = $fieldnames[$col] ?? "";

        if(!$colname)
            $this->_exception("no column in position $col");

        $result = array_column($result, $colname);
        if ($row) $result = $result[$row] ?? "";

        return $result;
    }

    private function _get_pdo(): PDO
    {
        $strcon = $this->_get_conn_string();
        $pdo = new PDO(
            $strcon,
            $this->config["user"],
            $this->config["password"],
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
    
    public function query(string $sql, ?int $col=null, ?int $row=null): array
    {
        $pdo = $this->_get_pdo();
        $this->_log($sql,"componentmysql.query");
        $cursor = $pdo->query($sql);

        if($cursor===false)
            $this->_exception("pdo.query error");

        while($row = $cursor->fetch(PDO::FETCH_ASSOC))
            $result[] = $row;

        //@deprecated https://dev.mysql.com/worklog/task/?id=12615
        //mejor es hacer un count(*) sin limit
        $sql = "SELECT FOUND_ROWS()";
        $this->_log($sql, "componentmysql.count");
        $this->foundrows = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
        return $this->_get_rowcol($result, $col, $row);
    }//query

    public function exec(string $sql)
    {
        $pdo = $this->_get_pdo();
        $this->_log($sql,"componentmysql.exec");
        $result = $pdo->exec($sql);
        if ($result === false)
            $this->_exception("pdo.exec error");

        $this->affected = $result;
        if(strstr($sql,"INSERT INTO "))
            $this->lastid = $pdo->lastInsertId();

        return $result;
    }//exec    

    private function _log($mxVar, ?string $title=null): void
    {
        if(defined("PATH_LOGS") && class_exists("\TheFramework\Components\ComponentLog")) {
            $oLog = new \TheFramework\Components\ComponentLog("sql", PATH_LOGS);
            $oLog->save($mxVar,"-- ". $title);
        }

        if(function_exists("get_log_producer")) {
            get_log_producer()->send($mxVar, "-- ". $title, "sql");
        }
    }

    private function _add_error(string $message){$this->iserror = true; $this->affected=-1; $this->errors[]=$message;}

    public function add_conn(string $k, string $v): self {$this->config[$k]=$v; return $this;}
    public function set_conn(array $config=[]): self {$this->config = $config; return $this;}

    public function get_affected(){return $this->affected;}
    public function get_foundrows(){return $this->foundrows;}
    public function get_lastid(){return $this->lastid;}

    public function is_error(){return $this->iserror;}
    public function get_errors(){return $this->errors;}
    public function get_error($i=0){return $this->errors[$i] ?? "";}

}//ComponentMysql
