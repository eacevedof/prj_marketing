<?php

namespace Tests\Unit\Apify\Application\Security;

use Tests\Unit\AbsUnitTest;
use App\Apify\Application\Security\LoginService;

final class LoginServiceTest extends AbsUnitTest
{
    public function test_get_token_nok(): void
    {
        $this->expectExceptionMessage("Domain localhost:200 is not authorized 2");
        $post = ["user" => "fulanito","password" => "menganitox"];
        $oServ = new LoginService("localhost:200", $post);
        $oServ->getTokenFromLoginOrFail();
    }

    public function test_get_token_nok_for_domain_asterisk(): void
    {
        $post = ["user" => "fulanito","password" => "menganitox"];
        $oServ = new LoginService("*", $post);
        $token = $oServ->getTokenFromLoginOrFail();
    }

    public function test_is_valid_token_ok(): void
    {
        $post = ["user" => "fulanito","password" => "menganitox"];
        $oServ = new LoginService("localhost:300", $post);
        $token = $oServ->getTokenFromLoginOrFail();
        $oServ = new LoginService("localhost:300");
        $isvalid = $oServ->isValidTokenOrFail($token);
        $this->assertTrue($isvalid);
    }

    public function test_valid_token_nok(): void
    {
        $post = ["user" => "fulanito","password" => "menganitox"];
        $oServ = new LoginService("localhost:300", $post);
        $token = $oServ->getTokenFromLoginOrFail();
        $token .= "xxxx";

        $oServ = new LoginService("localhost:300");
        $oServ->isValidTokenOrFail($token);
    }

}//LoginServiceTest
