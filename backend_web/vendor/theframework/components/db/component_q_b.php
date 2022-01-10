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

class ComponentQB
{
    private string $comment;
    private string $table; //Tabla sobre la que se realizará el crud
    private array $arinsertfv;
    
    private array $arnumeric; //si esta en este array no se escapa con '
    private array $arorderby;
    private array $argroupby;
    private array $arhaving;
    private array $arands;
    private array $arjoins;
    
    private bool $isdistinct;
    private bool $calcfoundrows;
    private array $arupdatefv;
    private array $arpks;
    private array $argetfields;
    private $arresult;
    private $arend;
    private $arlimit;

    private string $sql;

    private ?Object $oDB;
    
    protected $arErrors = [];
    protected $isError = false;

    protected $reserved = ["get", "order", "password"];

    /**
     * 
     * @param TheFramework\Components\Db\ComponentMysql $oDB
     */
    public function __construct(string $table = "")
    {
        $this->argetfields = [];

        $this->arend = [];
        $this->arresult = [];
        $this->arinsertfv = [];
        $this->arupdatefv = [];
        $this->pksfv = [];

        $this->arjoins = [];
        $this->arands = [];
        $this->arorderby = [];
        $this->argroupby = [];
        $this->arnumeric = [];
        $this->oDB = null;
    }
    
    private function get_orderby()
    {
        if(!$this->arorderby) return "";
        $arsql = [];
        $orderBy = " ORDER BY ";
        foreach($this->arorderby as $sField=>$sAD) {
            $this->clean_reserved($sField);
            $arsql[] = "$sField $sAD";
        }
        $orderBy = $orderBy.implode(",",$arsql);
        return $orderBy;
    }

    private function get_having()
    {
        if(!$this->arhaving) return "";
        $arsql = [];
        $sHaving = " HAVING ";
        foreach($this->arhaving as $sHavcond)
            $arsql[] = $sHavcond;
        $sHaving = $sHaving.implode(", ",$arsql);
        return $sHaving;
    }
    
    private function get_groupby()
    {
        if(!$this->argroupby) return "";
        $sGroupBy = "";
        $arsql = [];
        if($this->argroupby)
        {
            $sGroupBy = " GROUP BY ";
            foreach($this->argroupby as $sField) {
                $this->clean_reserved($sField);
                $arsql[] = $sField;
            }
            $sGroupBy = $sGroupBy.implode(",",$arsql);
        }
        return $sGroupBy;
    }

    private function get_joins()
    {
        if(!$this->arjoins) return "";
        $sjoin = " ".implode("\n",$this->arjoins);
        return $sjoin;        
    }
    
    private function get_end()
    {
        if(!$this->arend) return "";
        $sEnd = " ".implode("\n",$this->arend);
        return $sEnd;        
    }

    private function get_limit()
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
    
    private function is_numeric($fieldname){return in_array($fieldname,$this->arnumeric);}

    private function is_reserved($word){return in_array(strtolower($word),$this->reserved);}

    private function clean_reserved(&$mxfields)
    {
        if(is_array($mxfields)) {
            foreach ($mxfields as $i => $field) {
                if ($this->is_reserved($field))
                    $mxfields[$i] = "`$field`";
            }
        }
        elseif(is_string($mxfields)) {
            if ($this->is_reserved($mxfields))
                $mxfields = "`$mxfields`";
        }
    }

    private function is_tagged($value)
    {
        if(!is_string($value)) return false;
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

    private function get_untagged($tagged)
    {
        $ilen = strlen($tagged);
        return substr($tagged, 2, $ilen - 4);
    }

    public function autoinsert($table=null,$arfieldval=[])
    {
        //Limpio la consulta 
        $this->sql = "-- autoinsert";
        
        $comment = "";
        if($this->comment)
            $comment = "/*$this->comment*/";
        
        if(!$table)
            $table = $this->table;
        
        if($table)
        {
            if(!$arfieldval)
                $arfieldval = $this->arinsertfv;
            
            if($arfieldval)
            {    
                $sql = "$comment INSERT INTO ";
                $sql .= "$table ( ";

                $fields = array_keys($arfieldval);
                $this->clean_reserved($fields);
                $sql .= implode(",",$fields);

                $arValues = array_values($arfieldval);
                //los paso a entrecomillado
                foreach ($arValues as $i=>$strval)
                {
                    if($strval===null)
                        $arAux[] = "null";
                    else
                        $arAux[] = "'$strval'";
                }

                $sql .= ") VALUES (";
                $sql .= implode(",",$arAux);
                $sql .= ")";
                
                $this->sql = $sql;
                //si hay bd intenta ejecutar la consulta
                $this->query("w");
            }//si se han proporcionado correctamente los datos campo=>valor
        }//se ha proporcionado una tabla
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
                $arpks = $this->pksfv;

            $sql = "$comment UPDATE $table ";
            $sql .= "SET ";
            //creo las asignaciones de campos set extras
            $arAux = [];
            foreach($arfieldval as $sField=>$strval)
            {
                //echo "$sField  =  $strval\n";
                $this->clean_reserved($sField);
                if($strval===null)
                    $arAux[] = "$sField=null";
                elseif($this->is_tagged($strval)) {
                    $arAux[] = "$sField={$this->get_untagged($strval)}";
                }
                elseif($this->is_numeric($sField))
                    $arAux[] = "$sField=$strval";
                else    
                    $arAux[] = "$sField='$strval'";
            }

            $sql .= implode(",",$arAux);

            $sql .= " WHERE 1 ";

            //condiciones con las claves
            $arAux = [];
            foreach($arpks as $sField=>$strval)
            {
                $this->clean_reserved($sField);
                if($strval===null)
                    $arAux[] = "$sField IS null";
                elseif($this->is_tagged($strval)) {
                    $arAux[] = "$sField={$this->get_untagged($strval)}";
                }
                elseif($this->is_numeric($sField))
                    $arAux[] = "$sField=$strval";
                else    
                    $arAux[] = "$sField='$strval'";
            }
            
            $arAux = array_merge($arAux,$this->arands);
            if($arAux)
                $sql .= "AND ".implode(" AND ",$arAux);            
            
            $sql .= $this->get_end();
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
                $arpks = $this->pksfv;
            
            $sql = "$comment DELETE FROM $table ";

            //condiciones con las claves
            $arAux = [];
            foreach($arpks as $sField=>$strval)
            {
                $this->clean_reserved($sField);
                if($strval===null)
                    $arAux[] = "$sField IS null";
                elseif($this->is_tagged($strval)) {
                    $arAux[] = "$sField={$this->get_untagged($strval)}";
                }
                elseif($this->is_numeric($sField))
                    $arAux[] = "$sField=$strval";
                else    
                    $arAux[] = "$sField='$strval'";
            }                
            
            $sql .= " WHERE 1 ";
            
            $arAux = array_merge($arAux,$this->arands);
            if($arAux)
                $sql .= "AND ".implode(" AND ",$arAux);            
            
            $sql .= $this->get_end();
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
                $arpks = $this->pksfv;
            
            if($arpks)
            {    
                //@todo
                $sql = "$comment UPDATE $table ";
                $sql .= "SET  ";

                //condiciones con las claves
                $arAnd = [];
                foreach($arpks as $sField=>$strval)
                {
                    $this->clean_reserved($sField);
                    if($strval===null)
                        $arAnd[] = "$sField IS null";
                    elseif($this->is_tagged($strval)) {
                        $arAux[] = "$sField={$this->get_untagged($strval)}";
                    }
                    elseif($this->is_numeric($sField))
                        $arAux[] = "$sField=$strval";
                    else
                        $arAux[] = "$sField='$strval'";
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
                $arpks = $this->pksfv;
            
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
                foreach($arpks as $sField=>$strval)
                {
                    $this->clean_reserved($sField);
                    if($strval===null)
                        $arAnd[] = "$sField IS null";
                    elseif($this->is_numeric($sField))
                        $arAux[] = "$sField=$strval";
                    else    
                        $arAux[] = "$sField='$strval'";
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

        if(!$arpks) $arpks = $this->pksfv;

        $sql = "$comment SELECT ";
        if($this->calcfoundrows) $sql .= "SQL_CALC_FOUND_ROWS ";
        if($this->isdistinct) $sql .= "DISTINCT ";
        $this->clean_reserved($fields);
        $sql .= implode(",",$fields)." ";
        $sql .= "FROM $table";

        $sql .= $this->get_joins();
        //condiciones con las claves
        $arAux = [];
        foreach($arpks as $sField=>$strval) {
            $this->clean_reserved($sField);
            if($strval===null)
                $arAux[] = "$sField IS null";
            elseif($this->is_numeric($sField))
                $arAux[] = "$sField=$strval";
            else
                $arAux[] = "$sField='$strval'";
        }

        $arAux = array_merge($arAux,$this->arands);
        if($arAux) $sql .= " WHERE ".implode(" AND ",$arAux);

        $sql .= $this->get_groupby();
        $sql .= $this->get_having();
        $sql .= $this->get_orderby();
        $sql .= $this->get_end();
        $sql .= $this->get_limit();
        $this->sql = $sql;

        return $this->sql;
    }//get_selectfrom

    public function set_table(?string $table=null):self{$this->table=$table; return $this;}
    public function set_comment(string $sComment):self{$this->comment = $sComment; return $this;}
    
    public function set_insert_fv(array $arfieldval=[]):self{$this->arinsertfv = []; if(is_array($arfieldval)) $this->arinsertfv=$arfieldval; return $this;}
    public function add_insert_fv($fieldname,$strval,$isSanit=1):self{$this->arinsertfv[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}

    public function set_pks_fv(array $arfieldval=[]):self{$this->pksfv = []; if(is_array($arfieldval)) $this->pksfv=$arfieldval; return $this;}
    public function add_pk_fv($fieldname,$strval,$isSanit=1):self{$this->pksfv[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}
    
    public function set_update_fv(array $arfieldval=[]):self{$this->arupdatefv = []; if(is_array($arfieldval)) $this->arupdatefv=$arfieldval; return $this;}
    public function add_update_fv($fieldname,$strval,$isSanit=1):self{$this->arupdatefv[$fieldname]=($isSanit)?$this->get_sanitized($strval):$strval; return $this;}
    
    public function set_getfields(array $fields=[]):self{$this->argetfields = []; if(is_array($fields)) $this->argetfields=$fields; return $this;}
    public function add_getfield(string $fieldname):self{$this->argetfields[]=$fieldname; return $this;}

    public function set_joins(array $arjoins=[]):self{$this->arjoins = []; if(is_array($arjoins)) $this->arjoins=$arjoins; return $this;}
    public function set_orderby(array $arorderby=[]):self{$this->arorderby = []; if(is_array($arorderby)) $this->arorderby=$arorderby; return $this;}
    public function set_groupby(array $argroupby=[]):self{$this->argroupby = []; if(is_array($argroupby)) $this->argroupby=$argroupby; return $this;}
    public function set_having(array $arhaving=[]):self{$this->arhaving = []; if(is_array($arhaving)) $this->arhaving=$arhaving; return $this;}
    
    public function set_end(array $arend=[]):self{$this->arend = []; if(is_array($arend)) $this->arend=$arend; return $this;}
    public function set_limit($iPPage=1000, $iRegfrom=0):self{
        $this->arlimit=["regfrom"=>$iRegfrom, "perpage"=>$iPPage];
        if($iPPage==null) $this->arlimit = [];
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

    private function add_error($sMessage):self{$this->isError = true;$this->arErrors[]=$sMessage; return $this; return $this;}

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

    public function is_error(){return $this->isError;}
    public function get_result(){$this->query(); return $this->arresult;}
    public function get_errors($inJson=0){if($inJson) return json_encode($this->arErrors); return $this->arErrors;}
    public function get_error($i=0){return isset($this->arErrors[$i])?$this->arErrors[$i]:null;}

}//Crud 3.0.0
