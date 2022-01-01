<?php

namespace App\Components\Request;

final class RequestComponent
{
    public function get_post($sKey=null)
    {
        if(!$sKey) return $_POST ?? [];
        return $_POST[$sKey] ??  null;
    }

    public function get_files($sKey=null)
    {
        if(!$sKey) return $_FILES ?? [];
        return $_FILES[$sKey] ?? null;
    }

    public function get_get($sKey=null)
    {
        if(!$sKey) return $_GET ?? [];
        return $_GET[$sKey] ?? null;
    }

    public function is_post($sKey=null){ return $sKey ? isset($_POST[$sKey]) : count($_POST)>0;}

    public function is_get($sKey=null){ return $sKey ? isset($_GET[$sKey]) : count($_GET)>0;}

    public function is_file($sKey=null){ return $sKey ? isset($_FILES[$sKey]) : count($_FILES)>0;}

    public function get_header($key=null)
    {
        $all = getallheaders();
        if(!$key) return $all;
        foreach ($all as $k=>$v)
            if(strtolower($k) === strtolower($key))
                return $v;
        return null;
    }
}