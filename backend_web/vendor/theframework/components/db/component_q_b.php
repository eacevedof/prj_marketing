<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheFramework\Components\Db\ComponentQB
 * @file component_crud.php 2.12.0
 * @date 26-12-2021 20:31 SPAIN
 * @observations
 */
namespace TheFramework\Components\Db;

use \Exception;

class ComponentQB
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

    private array $arpks            = [];
    private array $arupdatefv       = [];

    private string $sql             = "";
    private array $arresult         = [];

    private ?Object $oDB = null;

    private array $errors           = [];
    private bool $iserror           = false;

    private array $reserved = ["get", "order", "password"];

    public function __construct(string $table = "")
    {
        $this->table = $table;
    }

    private function _get_joins(): string
    {
        if(!$this->arjoins) return "";
        return " ".implode("\n",$this->arjoins);
    }

    private function _get_having(): string
    {
        if(!$this->arhaving) return "";
        $arsql = [];
        foreach($this->arhaving as $havecond) $arsql[] = $havecond;
        return " HAVING ".implode(", ",$arsql);
    }

    private function _get_groupby(): string
    {
        if(!$this->argroupby) return "";
        $arsql = [];
        foreach($this->argroupby as $field) {
            $this->_clean_reserved($field);
            $arsql[] = $field;
        }
        return " GROUP BY ".implode(",",$arsql);
    }

    private function _get_orderby(): string
    {
        if(!$this->arorderby) return "";
        $arsql = [];
        foreach($this->arorderby as $field=>$AD) {
            $this->_clean_reserved($field);
            $arsql[] = "$field $AD";
        }
        return " ORDER BY ".implode(",",$arsql);
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
        $sLimit = " LIMIT ".implode(", ",$this->arlimit);
        /**
         * si por ejemplo deseo paginar de 10 en 10
         * para la pag:
         *  1 sería LIMIT 0,10   -- 1 a 10
         *  2 LIMIT 10,10        -- 11 a 20
         *  3 LIMIT 20,10        -- 21 a 30
         */
        return $sLimit;
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

    public function autoinsert(?string $table=null, array $arfieldval=[]): self
    {
        if(!$table) $table = $this->table;
        if(!$table) $this->_exception("missing table in autoinsert");
        
        $comment = $this->comment ? "/*$this->comment*/" : "/*autoinsert*/";
        $arfieldval = $arfieldval ?? $this->arinsertfv;
        if (!$arfieldval) $this->_exception("missing fields and values in autoinsert");
        
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
        $sql .= implode(",",$arAux);
        $sql .= ")";

        $this->sql = $sql;
        //to-do
        //$this->query("w");
        return $this;
    }//autoinsert

    public function autoupdate($table=null,$arfieldval=[],$arpks=[])
    {
        //Limpio la consulta
        $this->sql = "-- autoupdate";

        $comment = "";
        if($this->comment)
            $comment = "/*$this->comment*/";

        if(!$table)
            $table = $this->table;

        if($table)
        {
            if(!$arfieldval)
                $arfieldval = $this->arupdatefv;
            if(!$arpks)
                $arpks = $this->arpks;

            $sql = "$comment UPDATE $table ";
            $sql .= "SET ";
            //creo las asignaciones de campos set extras
            $arAux = [];
            foreach($arfieldval as $field=>$strval)
            {
                //echo "$field  =  $strval\n";
                $this->_clean_reserved($field);
                if($strval===null)
                    $arAux[] = "$field=null";
                elseif($this->_is_tagged($strval)) {
                    $arAux[] = "$field={$this->_get_untagged($strval)}";
                }
                elseif($this->_is_numeric($field))
                    $arAux[] = "$field=$strval";
                else
                    $arAux[] = "$field='$strval'";
            }

            $sql .= implode(",",$arAux);

            $sql .= " WHERE 1 ";

            //condiciones con las claves
            $arAux = [];
            foreach($arpks as $field=>$strval)
            {
                $this->_clean_reserved($field);
                if($strval===null)
                    $arAux[] = "$field IS null";
                elseif($this->_is_tagged($strval)) {
                    $arAux[] = "$field={$this->_get_untagged($strval)}";
                }
                elseif($this->_is_numeric($field))
                    $arAux[] = "$field=$strval";
                else
                    $arAux[] = "$field='$strval'";
            }

            $arAux = array_merge($arAux,$this->arands);
            if($arAux)
                $sql .= "AND ".implode(" AND ",$arAux);

            $sql .= $this->_get_end();
            $this->sql = $sql;
            //si hay bd intenta ejecutar la consulta
            $this->query("w");
        }//se ha proporcionado una tabla
        return $this;
    }//autoupdate

    public function autodelete($table=null,$arpks=[])
    {
        //Limpio la consulta
        $this->sql = "-- autodelete";

        $comment = "";
        if($this->comment)
            $comment = "/*$this->comment*/";

        if(!$table)
            $table = $this->table;

        if($table)
        {
            if(!$arpks)
                $arpks = $this->arpks;

            $sql = "$comment DELETE FROM $table ";

            //condiciones con las claves
            $arAux = [];
            foreach($arpks as $field=>$strval)
            {
                $this->_clean_reserved($field);
                if($strval===null)
                    $arAux[] = "$field IS null";
                elseif($this->_is_tagged($strval)) {
                    $arAux[] = "$field={$this->_get_untagged($strval)}";
                }
                elseif($this->_is_numeric($field))
                    $arAux[] = "$field=$strval";
                else
                    $arAux[] = "$field='$strval'";
            }

            $sql .= " WHERE 1 ";

            $arAux = array_merge($arAux,$this->arands);
            if($arAux)
                $sql .= "AND ".implode(" AND ",$arAux);

            $sql .= $this->_get_end();
            $this->sql = $sql;
            //si hay bd intenta ejecutar la consulta
            $this->query("w");

        }//se ha proporcionado una tabla
        return $this;
    }//autodelete

    public function autodelete_logic($table=null,$arpks=[])
    {
        //Limpio la consulta
        $this->sql = "-- autodelete_logic";

        if($this->comment)
            $comment = "/*$this->comment*/";

        if(!$table)
            $table = $this->table;

        if($table)
        {
            if(!$arpks)
                $arpks = $this->arpks;

            if($arpks)
            {
                //@todo
                $sql = "$comment UPDATE $table ";
                $sql .= "SET  ";

                //condiciones con las claves
                $arAnd = [];
                foreach($arpks as $field=>$strval)
                {
                    $this->_clean_reserved($field);
                    if($strval===null)
                        $arAnd[] = "$field IS null";
                    elseif($this->_is_tagged($strval)) {
                        $arAux[] = "$field={$this->_get_untagged($strval)}";
                    }
                    elseif($this->_is_numeric($field))
                        $arAux[] = "$field=$strval";
                    else
                        $arAux[] = "$field='$strval'";
                }

                $sql .= " WHERE ".implode(" AND ",$arAnd);

                $this->sql = $sql;
                //si hay bd intenta ejecutar la consulta
                $this->query("w");
            }//si se han proporcionado correctamente las claves
        }//se ha proporcionado una tabla
        return $this;
    }//autodelete_logic

    public function autoundelete_logic($table=null,$arpks=[])
    {
        //Limpio la consulta
        $this->sql = "-- autoundelete_logic";

        if($this->comment)
            $comment = "/*$this->comment*/";

        if(!$table)
            $table = $this->table;

        if($table)
        {
            if(!$arpks)
                $arpks = $this->arpks;

            if($arpks)
            {
                $codUserSession = getPostParam("userId");
                $sNow = date("Ymdhis");
                $sql = "$comment UPDATE $table 
                        SET 
                        delete_date=null
                        ,delte_user=null
                        ,update_date='$sNow'
                        ,update_user='$codUserSession'
                        ";

                //condiciones con las claves
                $arAnd = [];
                foreach($arpks as $field=>$strval)
                {
                    $this->_clean_reserved($field);
                    if($strval===null)
                        $arAnd[] = "$field IS null";
                    elseif($this->_is_numeric($field))
                        $arAux[] = "$field=$strval";
                    else
                        $arAux[] = "$field='$strval'";
                }

                $sql .= " WHERE ".implode(" AND ",$arAnd);

                $this->sql = $sql;
                if(is_object($this->oDB))
                    $this->oDB->exec($this->sql);
            }//si se han proporcionado correctamente las claves
        }//se ha proporcionado una tabla
        return $this;
    }//autoundelete_logic

    public function get_selectfrom($table=null,$fields=[],$arpks=[])
    {
        //Limpio la consulta
        $this->sql = "-- get_selectfrom";

        $comment = "";
        if($this->comment) $comment = "/*$this->comment*/";

        if(!$table) $table = $this->table;
        if(!$table) return $this->sql;

        if(!$fields) $fields = $this->argetfields;
        if(!$fields) return $this->sql;

        if(!$arpks) $arpks = $this->arpks;

        $sql = "$comment SELECT ";
        if($this->calcfoundrows) $sql .= "SQL_CALC_FOUND_ROWS ";
        if($this->isdistinct) $sql .= "DISTINCT ";
        $this->_clean_reserved($fields);
        $sql .= implode(",",$fields)." ";
        $sql .= "FROM $table";

        $sql .= $this->_get_joins();
        //condiciones con las claves
        $arAux = [];
        foreach($arpks as $field=>$strval) {
            $this->_clean_reserved($field);
            if($strval===null)
                $arAux[] = "$field IS null";
            elseif($this->_is_numeric($field))
                $arAux[] = "$field=$strval";
            else
                $arAux[] = "$field='$strval'";
        }

        $arAux = array_merge($arAux,$this->arands);
        if($arAux) $sql .= " WHERE ".implode(" AND ",$arAux);

        $sql .= $this->_get_groupby();
        $sql .= $this->_get_having();
        $sql .= $this->_get_orderby();
        $sql .= $this->_get_end();
        $sql .= $this->_get_limit();
        $this->sql = $sql;

        return $this->sql;
    }//get_selectfrom

    public function set_table(?string $table=null):self{$this->table=$table; return $this;}
    public function set_comment(string $sComment):self{$this->comment = $sComment; return $this;}

    public function set_insert_fv(array $arfieldval=[]):self{$this->arinsertfv = []; if(is_array($arfieldval)) $this->arinsertfv=$arfieldval; return $this;}
    public function add_insert_fv($fieldname,$strval,$isSanit=1):self{$this->arinsertfv[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_pks_fv(array $arfieldval=[]):self{$this->arpks = []; if(is_array($arfieldval)) $this->arpks=$arfieldval; return $this;}
    public function add_pk_fv($fieldname,$strval,$isSanit=1):self{$this->arpks[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_update_fv(array $arfieldval=[]):self{$this->arupdatefv = []; if(is_array($arfieldval)) $this->arupdatefv=$arfieldval; return $this;}
    public function add_update_fv($fieldname,$strval,$isSanit=1):self{$this->arupdatefv[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_getfields(array $fields=[]):self{$this->argetfields = []; if(is_array($fields)) $this->argetfields=$fields; return $this;}
    public function add_getfield(string $fieldname):self{$this->argetfields[]=$fieldname; return $this;}

    public function set_joins(array $arjoins=[]):self{$this->arjoins = []; if(is_array($arjoins)) $this->arjoins=$arjoins; return $this;}
    public function set_orderby(array $arorderby=[]):self{$this->arorderby = []; if(is_array($arorderby)) $this->arorderby=$arorderby; return $this;}
    public function set_groupby(array $argroupby=[]):self{$this->argroupby = []; if(is_array($argroupby)) $this->argroupby=$argroupby; return $this;}
    public function set_having(array $arhaving=[]):self{$this->arhaving = []; if(is_array($arhaving)) $this->arhaving=$arhaving; return $this;}

    public function set_end(array $arend=[]):self{$this->arend = []; if(is_array($arend)) $this->arend=$arend; return $this;}
    public function set_limit(int $ppage=1000, int $regfrom=0): self
    {
        $this->arlimit=["regfrom"=>$regfrom, "perpage"=>$ppage];
        if($ppage==null) $this->arlimit = [];
        return $this;
    }

    public function get_sql(){return $this->sql;}

    public function get_sanitized($strval)
    {
        if($strval===null) return null;
        // no se pq he escapado el % y el _ pero no debería
        $sFixed = str_replace("'","\'",$strval);
        //$sFixed = str_replace("%","\%",$sFixed);
        //$sFixed = str_replace("_","\_",$sFixed); si quiero guardar  SQL_CALC_FOUND_ROWS me hace SQL\_CALC_\
        return $sFixed;
    }//get_sanitized

    /**
     *
     * @param char $sType r:read para selects, w:write. escrituras
     * @return Solo se sale
     */
    private function query($sType="r")
    {
        if(is_object($this->oDB))
        {
            //insert,update,delete
            if(method_exists($this->oDB,"exec") && $sType=="w")
                $this->arresult = $this->oDB->exec($this->sql);
            //selects
            elseif(method_exists($this->oDB,"query") && $sType=="r")
                $this->arresult = $this->oDB->query($this->sql);
            else
                return $this->add_error("No match method/type operation");

            //propagamos el error
            if($this->oDB->is_error())
                $this->add_error($this->oDB->get_error());
        }
    }//query

    private function add_error($sMessage):self{$this->iserror = true;$this->errors[]=$sMessage; return $this; return $this;}

    public function is_distinct($isOn=true):self{$this->isdistinct=$isOn; return $this;}
    public function is_foundrows($isOn=true):self {$this->calcfoundrows=$isOn; return $this;}
    public function add_numeric($fieldname):self{$this->arnumeric[]=$fieldname; return $this;}
    public function set_and($arands=[]):self{$this->arands = []; if(is_array($arands)) $this->arands=$arands; return $this;}
    public function add_and($sAnd):self{$this->arands[]=$sAnd; return $this;}
    public function add_and1($fieldname,$strval,$sOper="="):self{$this->arands[]="$fieldname $sOper $strval"; return $this;}
    public function add_and_in(string $fieldname, array $values, bool $isnum=true):self
    {
        $values = array_unique($values);
        $glue = $isnum ? "," : "','";
        $in = implode($glue, $values);
        $in = $isnum ? "($in)" : "('$in')";
        $this->arands[] = "$fieldname IN $in";
        return $this;
    }

    public function add_join(string $sjoin, $sKey=null):self{if($sKey)$this->arjoins[$sKey]=$sjoin;else$this->arjoins[]=$sjoin; return $this;}
    public function add_orderby($fieldname,$order="ASC"):self{$this->arorderby[$fieldname]=$order; return $this;}
    public function add_groupby($fieldname):self{$this->argroupby[]=$fieldname; return $this;}
    public function add_having($sHavecond):self{$this->arhaving[]=$sHavecond; return $this;}

    public function add_end($sEnd,$sKey=null):self{if($sKey)$this->arend[$sKey]=$sEnd;else$this->arend[]=$sEnd; return $this;}
    public function set_dbobj($oDb=null):self{$this->oDB=$oDb; return $this;}

    public function is_error(){return $this->iserror;}
    public function get_result(){$this->query(); return $this->arresult;}
    public function get_errors($inJson=0){if($inJson) return json_encode($this->errors); return $this->errors;}
    public function get_error($i=0){return isset($this->errors[$i])?$this->errors[$i]:null;}

}//Crud 3.0.0
