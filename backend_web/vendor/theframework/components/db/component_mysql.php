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
use \Exception;
use \PDOException;

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

        $config["mysql:host"] = $this->config["server"] ?? "";
        $config["dbname"] = $this->config["database"] ?? "";
        $config["port"] = $this->config["port"] ?? "";
        //$config["ConnectionPooling"] = (isset($this->config["pool"])?$this->config["pool"]:"0");

        $strcon = "";
        foreach($config as $sK=>$sV)
            $strcon .= "$sK=$sV;";

        return $strcon;
    }//_get_conn_string

    private function get_rowcol(array $result, ?int $col=null, ?int $row=null)
    {
        if(is_int($col) || is_int($row))
        {
            $arColnames = $result[0];
            $arColnames = array_keys($arColnames);
            $sColname = (isset($arColnames[$col])?$arColnames[$col]:"");
            if($sColname)
                $result = array_column($result,$sColname);

            if(isset($result[$row]))
                $result = $result[$row];
        }
        return $result;
    }

    public function query(string $sql, ?int $col=null, ?int $row=null)
    {
        $result = [];
        try
        {
            //devuelve server y bd
            $strcon = $this->_get_conn_string();
            //pr($strcon,"component_mysql.query");die;
            //pr($this->config,"xxxxxxxxxxxxxxxx");die("yyyyyyyyyy");
            //https://stackoverflow.com/questions/38671330/error-with-php7-and-sql-server-on-windows
            $pdo = new \PDO($strcon, $this->config["user"], $this->config["password"]
                ,[\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION );

            $this->_log($sql,"ComponentMysql.exec");
            $oCursor = $pdo->query($sql);
            if($oCursor===false)
            {
                $this->_add_error("exec-error: $sql");
            }
            else
            {
                while($arRow = $oCursor->fetch(\PDO::FETCH_ASSOC))
                    $result[] = $arRow;

                $sql = "SELECT FOUND_ROWS()";
                $this->foundrows = $pdo->query($sql)->fetch(\PDO::FETCH_COLUMN);

                $this->affected = count($result);
                if($result)
                    $result = $this->get_rowcol($result, $col, $row);
            }
        }
        catch(PDOException $e)
        {
            $message = "exception:{$e->getMessage()}";
            $this->_add_error($message);
            $this->_log($sql,"ComponentMysql.query error: $message");
        }
        return $result;
    }//query

    public function exec(string $sql)
    {
        try
        {
            $strcon = $this->_get_conn_string();
            //https://stackoverflow.com/questions/19577056/using-pdo-to-create-table
            $pdo = new \PDO($strcon,$this->config["user"],$this->config["password"]
                ,[\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION );
            $this->_log($sql,"ComponentMysql.exec");
            $mxR = $pdo->exec($sql);

            $this->affected = $mxR;

            if(strstr($sql,"INSERT INTO ")) $this->lastid = $pdo->lastInsertId();

            if($mxR===false)
            {
                $this->_add_error("exec-error: $sql");
            }
            return $mxR;
        }
        catch( $e)
        {
            $message = "exception:{$e->getMessage()}";
            $this->_add_error($message);
            $this->_log($sql,"ComponentMysql.exec error: $message");
        }
    }//exec    

    private function _log($mxVar, ?string $title=null): void
    {
        if(defined("PATH_LOGS") && class_exists("\TheFramework\Components\ComponentLog"))
        {
            $oLog = new \TheFramework\Components\Component_Log("sql",PATH_LOGS);
            $oLog->save($mxVar,"-- ". $title);
        }
        if(function_exists("get_log_producer"))
        {
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
