<?php

namespace Tests\Restrict\Auth\Application;

use Exception;
use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\{AuthService, CsrfService};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;

final class CsrfServiceTest extends AbsUnitTest
{
    private AuthService $authService;

    public function setUp(): void
    {
        $this->_load_session();
        $this->authService = AuthService::getInstance();
    }

    private function _load_server(int $i = 0): void
    {
        $servers = [
            [
                "REMOTE_HOST" => "",
                "REMOTE_ADDR" => "127.0.0.1",
                "HTTP_USER_AGENT" => ":)",
            ],
            [
                "REMOTE_HOST" => "",
                "REMOTE_ADDR" => "",
                "HTTP_USER_AGENT" => "",
            ],
            [
                "REMOTE_HOST" => "fake-host",
                "REMOTE_ADDR" => "fake-addr",
                "HTTP_USER_AGENT" => "fake-agent",
            ],
        ];

        $server = $servers[$i] ?? [];
        $_SERVER["REMOTE_HOST"] = $server["REMOTE_HOST"] ?? "";
        $_SERVER["REMOTE_ADDR"] = $server["REMOTE_ADDR"] ?? "";
        $_SERVER["HTTP_USER_AGENT"] = $server["HTTP_USER_AGENT"] ?? "";
    }

    public function test_get_token_for_root(): void
    {
        $this->_load_server();
        $csrfService = SF::getInstanceOf(CsrfService::class);
        $this->assertNotEmpty($token = $csrfService->getCsrfToken());
        $this->assertTrue($csrfService->isValidCsrfToken($token));
    }

    public function test_get_token_for_root_in_empty_server(): void
    {
        $this->_load_server(1);
        $csrfService = SF::getInstanceOf(CsrfService::class);
        $this->assertNotEmpty($token = $csrfService->getCsrfToken());
        $this->assertTrue($csrfService->isValidCsrfToken($token));
    }

    public function test_is_valid_token_for_random_string(): void
    {
        $this->_load_server(1);
        $this->expectExceptionMessage("Invalid csrf 1");
        SF::getInstanceOf(CsrfService::class)->isValidCsrfToken("SOME-RareToken-8789$5");
    }

    public function test_wrong_token_for_swapping_servers(): void
    {
        $this->expectException(Exception::class);
        $this->_load_server();
        $service = SF::getInstanceOf(CsrfService::class);
        $tokenforserv0 = $service->getCsrfToken();
        //fake request
        $this->_load_server(2);
        $service = SF::getInstanceOf(CsrfService::class);
        $isvalid = $service->isValidCsrfToken($tokenforserv0);
    }

}
