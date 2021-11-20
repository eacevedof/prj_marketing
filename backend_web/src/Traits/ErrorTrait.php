<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ErrorTrait
 * @file ErrorTrait.php 1.0.0
 * @date 01-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Traits;

trait ErrorTrait
{
    protected array $arErrors = [];
    protected bool $isError = false;
        
    protected function add_error($sMessage){$this->isError = true;$this->arErrors[]=$sMessage;}

    public function is_error(){return $this->isError;}

    private function _get_flattened(array $array): array
    {
        $flatten = [];
        array_walk_recursive($array, function ($a) use (&$flatten) {
            $flatten[] = $a;
        });
        return $flatten;
    }

    public function get_errors($inJson=0)
    {
        $errors = $this->_get_flattened($this->arErrors);
        if($inJson)
            return json_encode($errors);
        return $errors;
    }

    public function get_error($i=0){isset($this->arErrors[$i])?$this->arErrors[$i]:NULL;}
    public function show_errors(){echo "<pre>".var_export($this->arErrors,1);}    
    
}//ErrorTrait
