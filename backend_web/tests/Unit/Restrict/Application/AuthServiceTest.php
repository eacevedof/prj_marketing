<?php

namespace Tests\Unit\Restrict\Application;

use Tests\Unit\AbsUnitTest;
use App\Shared\Domain\Enums\SessionType;
use App\Restrict\Auth\Application\AuthService;

final class AuthServiceTest extends AbsUnitTest
{
    public function test_get_null_user(): void
    {
        $this->log("test_get_null_user");
        $this->_load_session(1);

        $authService = AuthService::getInstance();
        $user = $authService->getAuthUserArray();
        $this->assertTrue(count($user) === 0);
    }

    public function test_get_user(): void
    {
        $this->log("test_get_user");
        $this->_load_session(0);

        $authService = AuthService::getInstance();
        $user = $authService->getAuthUserArray();
        $this->assertArrayHasKey("id", $user);
        $this->assertNotNull($user["id"]);
    }

    public function test_is_root(): void
    {
        $this->log("test_is_root");
        $authService = AuthService::getInstance();
        $this->assertTrue($authService->isAuthUserRoot());
    }

    public function test_is_not_sysadmin(): void
    {
        $this->log("test_is_not_sysadmin");
        $authService = AuthService::getInstance();
        $this->assertFalse($authService->isAuthUserSysadmin());
    }

    public function test_is_business_owner(): void
    {
        $this->log("test_is_business_owner");
        $authService = AuthService::getInstance();
        $this->assertFalse($authService->isAuthUserBusinessOwner());
    }

    public function test_no_owner_in_root_user(): void
    {
        $this->log("test_no_id_owner");
        $authService = AuthService::getInstance();
        $this->assertNull($authService->getIdOwner());
    }

    public function test_has_permissions_node(): void
    {
        $this->log("test_has_permissions_node");
        $authService = AuthService::getInstance();
        $this->assertArrayHasKey(SessionType::AUTH_USER_PERMISSIONS, $authService->getAuthUserArray());
    }
}
