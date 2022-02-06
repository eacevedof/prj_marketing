<?php
namespace Tests\Restrict\Auth\Application;

use App\Shared\Infrastructure\Enums\ProfileType;
use App\Shared\Infrastructure\Enums\SessionType;
use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\AuthService;

final class AuthServiceTest extends AbsUnitTest
{
    private function _load_session(int $i): void
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

    public function test_get_null_user(): void
    {
        $this->log("test_get_null_user");
        $this->_load_session(1);

        $authService = AuthService::getme();
        $user = $authService->get_user();
        $this->assertTrue(count($user)===0);
    }

    public function test_get_user(): void
    {
        $this->log("test_get_user");
        $this->_load_session(0);

        $authService = AuthService::getme();
        $user = $authService->get_user();
        $this->assertArrayHasKey("id", $user);
        $this->assertNotNull($user["id"]);
    }

    public function test_is_root(): void
    {
        $this->log("test_is_root");
        $authService = AuthService::getme();
        $this->assertTrue($authService->get_user()["id_profile"] === ProfileType::ROOT);
    }
}