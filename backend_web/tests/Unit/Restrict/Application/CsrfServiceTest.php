<?php
namespace Tests\Restrict\Auth\Application;

use App\Shared\Infrastructure\Factories\ServiceFactory;
use Tests\Unit\AbsUnitTest;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Auth\Application\CsrfService;

final class CsrfServiceTest extends AbsUnitTest
{
    private AuthService $authService;

    public function setUp(): void
    {
        $this->_load_session();
        $this->authService = AuthService::getme();
    }

    private function _load_server(int $i=0): void
    {
        $servers = [
            [
                "REMOTE_HOST" => "",
                "REMOTE_ADDR" => "127.0.0.1",
                "HTTP_USER_AGENT" => ":)",
            ],
            [
                "REMOTE_HOST" => "somehost",
                "REMOTE_ADDR" => "127.0.0.1",
                "HTTP_USER_AGENT" => "Mozilla",
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
        $csrfService = ServiceFactory::get(CsrfService::class);
        $this->assertNotEmpty($token = $csrfService->get_token());
        $this->logpr($token);
    }
}