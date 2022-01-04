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

    public function is_post($sKey=null): bool { return $sKey ? isset($_POST[$sKey]) : count($_POST)>0;}

    public function is_get($sKey=null): bool { return $sKey ? isset($_GET[$sKey]) : count($_GET)>0;}

    public function is_file($sKey=null): bool { return $sKey ? isset($_FILES[$sKey]) : count($_FILES)>0;}

    public function get_method(){ return strtolower($_SERVER["REQUEST_METHOD"]) ?? "";}

    public function is_put(): bool { return $this->get_method()==="put";}

    public function is_patch(): bool { return $this->get_method()==="patch";}

    public function is_delete(): bool { return $this->get_method()==="delete";}

    public function is_postm(): bool { return $this->get_method()==="post";}

    public function get_header($key=null): ?string
    {
        $all = getallheaders();
        if(!$key) return $all;
        foreach ($all as $k=>$v)
            if(strtolower($k) === strtolower($key))
                return $v;
        return null;
    }

    public function get_referer(): ?string
    {
        return $_SERVER["HTTP_REFERER"] ?? null;
    }

    public function is_json(): bool
    {
        $accept = $this->get_header("accept");
        $accept == strtolower($accept);
        return strstr($accept,"application/json");
    }
}