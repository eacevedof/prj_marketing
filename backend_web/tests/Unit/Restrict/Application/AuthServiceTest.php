<?php
namespace Tests\Restrict\Auth\Application;

use App\Shared\Infrastructure\Enums\SessionType;
use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\AuthService;

final class AuthServiceTest extends  AbsUnitTest
{
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->authService = AuthService::getme();
        $_SESSION[SessionType::AUTH_USER] = [
            "id" => "1",
            "fullname" => "Main Root ONE",
            "description" => "Main Root ONE",
            "email" => "eaf@eaf.com",
            "secret" => "$2y$10\$BEClm.fzRU2shGk5nMLGRe4f0JnkXofGMBkLZ6sC86f8/aeetCMhC",
            "id_language" => "2",
            "id_profile" => "1",
            "uuid" => "U00001",
            "id_parent" => NULL,
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
        ];
    }

    public function test_get_user(): void
    {
        $this->log("test_get_user");
        $user = $this->authService->get_user();
        $this->assertArrayHasKey("id", $user);
    }
}