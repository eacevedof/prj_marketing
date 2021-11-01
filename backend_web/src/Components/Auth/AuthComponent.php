<?php

namespace App\Components\Auth;

final class AuthComponent
{
    public function is_user_allowed(?array $user, string $permission): bool
    {
        if($user) return true;
        return false;
    }
}