<?php
namespace Tests\Unit;

use App\Shared\Infrastructure\Enums\SessionType;
use Tests\Boot\AbsTestBase;

abstract class AbsUnitTest extends AbsTestBase
{
    protected function _load_session(int $i=0): void
    {
        $users = [
            [
                "id" => "1",
                "fullname" => "Main Root ONE",
                "description" => "Main Root ONE",
                "email" => "eaf@eaf.com",
                "secret" => "$2y$10\$BEClm.fzRU2shGk5nMLGRe4f0JnkXofGMBkLZ6sC86f8/aeetCMhC",
                "id_language" => "2",
                "id_profile" => "1",
                "uuid" => "U00001",
                "id_parent" => null,
                "e_language" => "es",
                "auth_user_permissions" => [
                    0 => "dashboard:read",
                    1 => "dashboard:write",
                    2 => "users:read",
                    3 => "users:write",
                    4 => "promotions:read",
                    5 => "promotions:write",
                ],
                "lang" => "es",
            ]
        ];

        $_SESSION[SessionType::AUTH_USER] = $users[$i] ?? null;
    }
}