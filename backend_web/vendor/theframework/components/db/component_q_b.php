<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheFramework\Components\Db\ComponentQB
 * @file component_q_b.php 3.0.0
 * @date 10-01-2022 20:31 SPAIN
 * @observations
 */
namespace TheFramework\Components\Db;

use \Exception;

final class ComponentQB
{
    private string $comment         = "";
    private string $table           = ""; //Tabla sobre la que se realizará el crud
    private bool $isdistinct        = false;
    private bool $calcfoundrows     = false;
    private array $argetfields      = [];
    private array $arjoins          = [];
    private array $arands           = [];
    private array $argroupby        = [];
    private array $arhaving         = [];
    private array $arorderby        = [];
    private array $arlimit          = [];
    private array $arend            = [];

    private array $arnumeric        = []; //si esta en este array no se escapa con '
    private array $arinsertfv       = [];

    private array $arupdatefv       = [];
    private array $arpks            = [];
    private array $select           = [];

    private string $sql             = "";
    private string $sqlcount        = "";

    private ?Object $oDB = null;

    private array $reserved = ["get", "order", "password"];

    const READ = "r";
    const WRITE = "w";

    public function __construct(string $table="")
    {
        $this->table = $table;
    }

    private function _get_joins(): string
    {
        if(!$this->arjoins) return "";
        return implode("\n",$this->arjoins);
    }

    private function _get_having(): string
    {
        if(!$this->arhaving) return "";
        $arsql = [];
        foreach($this->arhaving as $havecond) $arsql[] = $havecond;
        return "HAVING ".implode(", ",$arsql);
    }

    private function _get_groupby(): string
    {
        if(!$this->argroupby) return "";
        $arsql = [];
        foreach($this->argroupby as $field) {
            $this->_clean_reserved($field);
            $arsql[] = $field;
        }
        return "GROUP BY ".implode(",",$arsql);
    }

    private function _get_orderby(): string
    {
        if(!$this->arorderby) return "";
        $arsql = [];
        foreach($this->arorderby as $field=>$AD) {
            $this->_clean_reserved($field);
            $arsql[] = "$field $AD";
        }
        return "ORDER BY ".implode(",",$arsql);
    }

    private function _get_end(): string
    {
        if(!$this->arend) return "";
        return " ".implode("\n",$this->arend);
    }

    private function _get_limit(): string
    {
        if(!$this->arlimit) return "";
        // LIMIT regfrom (secuenta desde 0), perpage
        return " LIMIT ".implode(", ",$this->arlimit);
        /**
         * si por ejemplo deseo paginar de 10 en 10
         * para la pag:
         *  1 sería LIMIT 0,10   -- 1 a 10
         *  2 LIMIT 10,10        -- 11 a 20
         *  3 LIMIT 20,10        -- 21 a 30
         */
    }

    private function _is_numeric(string $fieldname): bool{return in_array($fieldname,$this->arnumeric);}

    private function _is_reserved(string $word): bool{return in_array(strtolower($word),$this->reserved);}

    private function _clean_reserved(&$mxfields): void
    {
        if(is_string($mxfields)) {
            if ($this->_is_reserved($mxfields))
                $mxfields = "`$mxfields`";
            return;
        }

        if(is_array($mxfields)) {
            foreach ($mxfields as $i => $field) {
                if ($this->_is_reserved($field))
                    $mxfields[$i] = "`$field`";
            }
        }
    }

    private function _is_tagged(string $value): bool
    {
        //$value = trim($value);
        $tagini = substr($value,0,2);
        $tagend = substr($value, -2);
        $ilen = strlen($value);
        if($ilen>4 && $tagini==="%%" && $tagend==="%%") {
            $field = substr($value, 2, $ilen - 4);
            return (trim($field) !== "");
        }
        return false;
    }

    private function _get_untagged(string $tagged): string
    {
        $ilen = strlen($tagged);
        return substr($tagged, 2, $ilen - 4);
    }

    private function _exception(string $message, int $code=500): void
    {
        throw new Exception($message, $code);
    }

    private function _get_pkconds(array $arpks): array
    {
        $arconds = [];
        $arpks = array_unique($arpks);
        foreach($arpks as $field=>$strval) {
            $this->_clean_reserved($field);
            if($strval===null)
                $arconds[] = "$field IS NULL";
            elseif($this->_is_tagged($strval)) {
                $arconds[] = "$field={$this->_get_untagged($strval)}";
            }
            elseif($this->_is_numeric($field))
                $arconds[] = "$field=$strval";
            else
                $arconds[] = "$field='$strval'";
        }
        return $arconds;
    }

    public function insert(?string $table=null, ?array $arfieldval=null): self
    {
        $this->sql = "/*error insert*/";
        if(!$table) $table = $this->table;
        if(!$table) $this->_exception("missing table in insert");

        $comment = $this->comment ? "/*$this->comment*/" : "/*insert*/";
        $arfieldval = $arfieldval ?? $this->arinsertfv;
        if (!$arfieldval) $this->_exception("missing fields and values in insert");

        $sql = "$comment INSERT INTO ";
        $sql .= "$table ( ";

        $fields = array_keys($arfieldval);
        $this->_clean_reserved($fields);
        $sql .= implode(",",$fields);

        $values = array_values($arfieldval);
        //los paso a entrecomillado
        foreach ($values as $strval)
            $araux[] = $strval===null ? "null" : "'$strval'";

        $sql .= ") VALUES (";
        $sql .= implode(",",$araux);
        $sql .= ")";

        $this->sql = $sql;
        //to-do
        //$this->query("w");
        return $this;
    }//insert

    public function update(?string $table=null, ?array $arfieldval=null, ?array $arpks=null): self
    {
        $this->sql = "/*error update*/";
        if(!$table) $table = $this->table;
        if(!$table) $this->_exception("missing table in update");

        $comment = $this->comment ? "/*$this->comment*/" : "/*update*/";
        $arfieldval = $arfieldval ?? $this->arupdatefv;
        if (!$arfieldval) $this->_exception("missing fields and values in update");
        $arpks = $arpks ?? $this->arpks;

        $sql = "$comment UPDATE $table ";
        $sql .= "SET ";
        //creo las asignaciones de campos set extras
        $arsets = [];
        foreach($arfieldval as $field=>$strval) {
            $this->_clean_reserved($field);
            if($strval===null)
                $arsets[] = "$field=null";
            elseif($this->_is_tagged($strval)) {
                $arsets[] = "$field={$this->_get_untagged($strval)}";
            }
            elseif($this->_is_numeric($field))
                $arsets[] = "$field=$strval";
            else
                $arsets[] = "$field='$strval'";
        }

        $sql .= implode(",", $arsets);
        $sql .= " WHERE 1 ";

        //condiciones con las claves
        $arconds = $this->_get_pkconds($arpks);

        $arconds = array_merge($arconds, $this->arands);
        if($arconds)
            $sql .= "AND ".implode(" AND ",$arconds);

        $sql .= $this->_get_end();
        $this->sql = $sql;
        //si hay bd intenta ejecutar la consulta
        //$this->query("w");
        return $this;
    }//update

    public function delete(?string $table=null, ?array $arpks=null): self
    {
        $this->sql = "/*error delete*/";
        if(!$table) $table = $this->table;
        if(!$table) $this->_exception("missing table in delete");

        $comment = $this->comment ? "/*$this->comment*/" : "/*delete*/";
        $arpks = $arpks ?? $this->arpks;

        $sql = "$comment DELETE FROM $table ";

        //condiciones con las claves
        $arconds = $this->_get_pkconds($arpks);

        $sql .= " WHERE 1 ";

        $araux = array_merge($arconds, $this->arands);
        if($araux)
            $sql .= "AND ".implode(" AND ",$araux);

        $sql .= $this->_get_end();
        $this->sql = $sql;
        //si hay bd intenta ejecutar la consulta
        //$this->query("w");
        return $this;
    }//delete

    private function _get_sql_count(): string
    {
        $arsql = $this->select;
        unset($arsql["orderby"], $arsql["limit"]);
        $subselect = trim(implode(" ",$arsql));
        $sql = [
            "SELECT",
            "COUNT(*) AS foundrows",
            "FROM (\n",
            $subselect,
            "\n) AS sqlcount"
        ];
        return implode(" ",$sql);
    }

    private function _remove_empty_item(array &$array): void
    {
        foreach ($array as $i => $item)
            if ($item==="" || is_null($item))
                unset($array[$i]);
    }

    public function select(?string $table=null, ?array $fields=null, ?array $arpks=null): self
    {
        $this->sql = "/*error select*/";
        $this->sqlcount = "/*error selectcount*/";

        if(!$table) $table = $this->table;
        if(!$table) $this->_exception("missing table in select");

        $fields = $fields ?? $this->argetfields;
        if (!$fields) $this->_exception("missing fields in select");

        $comment = $this->comment ? "/*$this->comment*/" : "/*select*/";
        $arpks = $arpks ?? $this->arpks;

        $this->select[] = "$comment SELECT";
        if($this->isdistinct) $this->select[] = "DISTINCT";
        $this->_clean_reserved($fields);
        $this->select[] = implode(",",$fields);
        $this->select[] = "\nFROM $table";

        $this->select[] = $this->_get_joins();

        $arconds = $this->_get_pkconds($arpks);
        $araux = array_merge($arconds, $this->arands);
        if($araux) $this->select[] = "\nWHERE ".implode(" AND ",$araux);

        $this->select[] = $this->_get_groupby();
        $this->select[] = $this->_get_having();
        $this->select["orderby"] = $this->_get_orderby();
        $this->select[] = $this->_get_end();
        $this->select["limit"] = $this->_get_limit();

        $this->_remove_empty_item($this->select);

        $this->sql = implode(" ",$this->select);
        $this->sqlcount = "";
        if ($this->calcfoundrows)
            $this->sqlcount = $this->_get_sql_count();
        return $this;
    }//get_selectfrom

    public function set_table(string $table): self {$this->table=$table; return $this;}
    public function set_comment(string $comment): self {$this->comment = $comment; return $this;}

    public function set_insert_fv(array $arfieldval=[]): self {$this->arinsertfv = []; if(is_array($arfieldval)) $this->arinsertfv=$arfieldval; return $this;}
    public function add_insert_fv(string $fieldname, $strval, bool $dosanit=true): self {$this->arinsertfv[$fieldname]=($dosanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_pks_fv(array $arfieldval=[]): self {$this->arpks = []; if(is_array($arfieldval)) $this->arpks=$arfieldval; return $this;}
    public function add_pk_fv(string $fieldname, $strval, bool $dosanit=true): self {$this->arpks[$fieldname]=($dosanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_update_fv(array $arfieldval=[]): self {$this->arupdatefv = []; if(is_array($arfieldval)) $this->arupdatefv=$arfieldval; return $this;}
    public function add_update_fv(string $fieldname, $strval, bool $dosanit=true): self {$this->arupdatefv[$fieldname]=($dosanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_getfields(array $fields=[]): self {$this->argetfields = []; if(is_array($fields)) $this->argetfields=$fields; return $this;}
    public function add_getfield(string $fieldname): self {$this->argetfields[]=$fieldname; return $this;}

    public function set_joins(array $arjoins=[]): self {$this->arjoins = []; if(is_array($arjoins)) $this->arjoins=$arjoins; return $this;}
    public function set_orderby(array $arorderby=[]): self {$this->arorderby = []; if(is_array($arorderby)) $this->arorderby=$arorderby; return $this;}
    public function set_groupby(array $argroupby=[]): self {$this->argroupby = []; if(is_array($argroupby)) $this->argroupby=$argroupby; return $this;}
    public function set_having(array $arhaving=[]): self {$this->arhaving = []; if(is_array($arhaving)) $this->arhaving=$arhaving; return $this;}

    public function set_end(array $arend=[]): self {$this->arend = []; if(is_array($arend)) $this->arend=$arend; return $this;}
    public function set_limit(int $ppage=1000, int $regfrom=0): self
    {
        $this->arlimit=["regfrom"=>$regfrom, "perpage"=>$ppage];
        if($ppage==null) $this->arlimit = [];
        return $this;
    }

    public function sql():string {return $this->sql;}
    public function sqlcount():string {return $this->sqlcount;}

    public function get_sanitized(?string $strval): ?string
    {
        if($strval===null) return null;
        // no se pq he escapado el % y el _ pero no debería
        //$strval = str_replace("\'","",$strval);
        //$strval = stripslashes($strval);
        $strval = str_replace("\\","\\\\",$strval);
        $strval = str_replace("'","\'",$strval);
        //$strfixed = str_replace("%","\%",$strfixed);
        //$strfixed = str_replace("_","\_",$strfixed); si quiero guardar  SQL_CALC_FOUND_ROWS me hace SQL\_CALC_\
        return $strval;
    }//get_sanitized

    /**
     * @param char $mode READ para selects, WRITE update,insert,delete
     * @return mixto
     */
    public function exec(string $mode=self::READ)
    {
        $result = [];
        if (!$this->oDB) $this->_exception("no db object not configured for get_result");
        if (!$this->sql) $this->_exception("empty sql in get_result");
        if (!in_array($mode, [self::READ, self::WRITE]))  $this->_exception("unrecognized mode");

        //insert,update,delete
        if(method_exists($this->oDB,"exec") && $mode==self::WRITE)
            return $this->oDB->exec($this->sql);

        if(method_exists($this->oDB,"query") && $mode==self::READ)
            return $this->oDB->query($this->sql);

        $this->_exception("missing exec or query method in db object");
    }//get_result

    public function set_db(Object $db): self
    {
        $this->oDB = $db;
        return $this;
    }

    public function distinct(bool $ison=true): self{$this->isdistinct=$ison; return $this;}
    public function calcfoundrows(bool $ison=true): self {$this->calcfoundrows=$ison; return $this;}
    public function add_numeric(string $fieldname): self{$this->arnumeric[]=$fieldname; return $this;}
    public function set_and(array $arands=[]): self{$this->arands = []; if(is_array($arands)) $this->arands=$arands; return $this;}
    public function add_and(string $condition): self{$this->arands[]=$condition; return $this;}
    public function add_and1(string $fieldname, $strval, string $sOper="="): self {$this->arands[]="$fieldname $sOper $strval"; return $this;}
    public function add_in(string $fieldname, array $values): self
    {
        $isnum = in_array($fieldname, $this->arnumeric);
        $values = array_unique($values);
        $glue = $isnum ? "," : "','";
        $in = implode($glue, $values);
        $in = $isnum ? "($in)" : "('$in')";
        $this->arands[] = "$fieldname IN $in";
        return $this;
    }

    public function add_join(string $sjoin, ?string $key=null): self {if($key)$this->arjoins[$key]=$sjoin;else$this->arjoins[]=$sjoin; return $this;}
    public function add_orderby(string $fieldname, string $order="ASC"): self {$this->arorderby[$fieldname]=$order; return $this;}
    public function add_groupby(string $fieldname): self {$this->argroupby[]=$fieldname; return $this;}
    public function add_having(string $having): self {$this->arhaving[]=$having; return $this;}
    public function add_end(string $strend, ?string $key=null): self {if($key)$this->arend[$key]=$strend;else$this->arend[]=$strend; return $this;}
}//Crud 3.0.0
