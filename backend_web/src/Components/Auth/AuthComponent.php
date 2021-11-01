<?php

namespace App\Components\Auth;
use App\Enums\Actions;

final class AuthComponent
{
    public function is_user_allowed(?array $user, Actions $action): bool
    {
        if($user) return true;
        return false;
    }
}