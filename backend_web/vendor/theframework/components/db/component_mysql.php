<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheFramework\Components\Db\ComponentMysql
 * @file component_mysql.php v3.0.0
 * @date 14-01-2022 12:00 SPAIN
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
    private string $sqlcount = "";

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

        if(!($this->config["server"] ?? "")) $this->_exception("missing server in config");
        if(!($this->config["database"] ?? "")) $this->_exception("missing database in config");
        if(!($this->config["user"] ?? "")) $this->_exception("missing user in config");
        if(!isset($this->config["password"])) $this->_exception("missing password in config");

        $config["mysql:host"]   = $this->config["server"];
        $config["dbname"]       = $this->config["database"];
        $config["port"]         = $this->config["port"] ?? "3386";

        $strcon = "";
        foreach($config as $sK=>$sV)
            $strcon .= "$sK=$sV;";

        //mysql:host=cont-mariadb-univ;dbname=db_marketing;port=3306;
        return $strcon;
    }//_get_conn_string

    private function _get_rowcol(array $result, ?int $icol=null, ?int $irow=null)
    {
        if (!$result) return $result;

        $row0 = $result[0];
        $fieldnames = array_keys($row0);

        if ($isrow = !is_null($irow)) $result = $result[$irow] ?? [];

        if (!is_null($icol)) {
            $colname = $fieldnames[$icol] ?? "";
            if(!$colname) $this->_exception("no column in position $icol");
            if($isrow) return $result[$colname];
            return array_column($result, $colname);
        }

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
    
    public function query(string $sql, ?int $icol=null, ?int $irow=null): array|string
    {
        $this->foundrows = 0;
        $pdo = $this->_get_pdo();
        $this->_log($sql,"componentmysql.query");

        if(($cursor = $pdo->query($sql))===false)
            $this->_exception("pdo.query error");

        $result = [];
        while($row = $cursor->fetch(PDO::FETCH_ASSOC))
            $result[] = $row;

        //@deprecated https://dev.mysql.com/worklog/task/?id=12615
        //mejor es hacer un count(*) sin limit
        if ($this->sqlcount) {
            $this->_log($this->sqlcount, "componentmysql.count");
            $this->foundrows = $pdo->query($this->sqlcount)->fetch(PDO::FETCH_COLUMN);
        }
        return $this->_get_rowcol($result, $icol, $irow);
    }//query

    public function exec(string $sql)
    {
        $this->affected = 0;
        $this->lastid = -1;

        $pdo = $this->_get_pdo();
        $this->_log($sql,"componentmysql.exec");
        if (($result = $pdo->exec($sql)) === false)
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

    public function add_conn(string $k, string $v): self {$this->config[$k]=$v; return $this;}
    public function set_conn(array $config=[]): self {$this->config = $config; return $this;}

    //on insert
    public function get_lastid(){return $this->lastid;}

    //on select
    public function set_sqlcount(string $sql=""): self{$this->sqlcount = $sql; return $this;}
    public function get_foundrows(){return $this->foundrows;}

    //on update/delete
    public function get_affected(){return $this->affected;}


}//ComponentMysql
