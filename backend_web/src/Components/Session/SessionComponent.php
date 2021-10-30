<?php

namespace App\Components\Session;


final class SessionComponent
{
    public function add_value(string $key, $mxvalue): SessionComponent
    {
        $_SESSION[$key] = $mxvalue;
        return  $this;
    }
    
    public function destroy(): SessionComponent
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return  $this;
    }

    public function start(): SessionComponent
    {
        session_start();
        return $this;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function get_once(string $key)
    {
        $value =  $this->get($key);
        unset($_SESSION[$key]);
    }

}