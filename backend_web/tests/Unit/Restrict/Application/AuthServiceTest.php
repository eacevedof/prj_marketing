<?php
namespace Tests\Restrict\Auth\Application;

use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\AuthService;

final class AuthServiceTest extends  AbsUnitTest
{
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->authService = AuthService::getme();
    }

    public function test_get_user(): void
    {
        $this->log("test_get_user");
        $user = $this->authService->get_user();
        $this->assertArrayHasKey("id", $user);
    }
}