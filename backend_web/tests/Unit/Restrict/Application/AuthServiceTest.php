<?php
namespace Tests\Restrict\Auth\Application;

use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Enums\SessionType;

final class AuthServiceTest extends AbsUnitTest
{


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
        $this->assertTrue($authService->is_root());
    }

    public function test_is_not_sysadmin(): void
    {
        $this->log("test_is_not_sysadmin");
        $authService = AuthService::getme();
        $this->assertFalse($authService->is_sysadmin());
    }

    public function test_is_business_owner(): void
    {
        $this->log("test_is_business_owner");
        $authService = AuthService::getme();
        $this->assertFalse($authService->is_business_owner());
    }

    public function test_no_owner_in_root_user(): void
    {
        $this->log("test_no_id_owner");
        $authService = AuthService::getme();
        $this->assertNull($authService->get_idowner());
    }

    public function test_has_permissions_node(): void
    {
        $this->log("test_has_permissions_node");
        $authService = AuthService::getme();
        $this->assertArrayHasKey(SessionType::AUTH_USER_PERMISSIONS, $authService->get_user());
    }
}