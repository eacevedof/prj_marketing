<?php

namespace App\Components\Auth;
use App\Factories\SessionFactory as SF;
use App\Enums\KeyType;

final class AuthComponent
{
    public function is_user_allowed(string $action): bool
    {
        $session = SF::get();
        if(!($user = $session->get(KeyType::AUTH_USER))) return false;
        if(!$user["id"]) return false;
        $permissions = SF::get()->get(KeyType::AUTH_USER_PERMISSIONS);
        return in_array($action, $permissions);
    }
}