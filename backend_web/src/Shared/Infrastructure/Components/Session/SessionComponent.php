<?php

namespace App\Shared\Infrastructure\Components\Session;

final class SessionComponent
{
    public function addValue(string $key, mixed $mxValue): self
    {
        $_SESSION[$key] = $mxValue;
        return $this;
    }

    public function remove(string $key): self
    {
        unset($_SESSION[$key]);
        return $this;
    }

    public function destroy(): self
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        session_start();
        session_regenerate_id(true);
        session_destroy();
        return $this;
    }

    public function start(): self
    {
        session_start();
        return $this;
    }

    public function get(string $key = ""): mixed
    {
        if (!$key) {
            return $_SESSION;
        }
        return $_SESSION[$key] ?? null;
    }

    public function getOnce(string $key): mixed
    {
        $value = $this->get($key);
        unset($_SESSION[$key]);
        return $value;
    }

}
