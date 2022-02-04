<?php
namespace Tests\Apify\Application\Security;

use PHPUnit\Framework\TestCase;
use Tests\Traits\LogTrait;

use App\Apify\Application\Security\LoginService;

final class LoginServiceTest extends TestCase
{
    use LogTrait;

    public function test_get_token_nok()
    {
        $post=["user"=>"fulanito","password"=>"menganitox"];
        $oServ = new LoginService("localhost:200",$post);
        $oServ->get_token();
    }

    public function test_get_token_nok_for_domain_asterisk()
    {
        $post=["user"=>"fulanito","password"=>"menganitox"];
        $oServ = new LoginService("*",$post);
        $token = $oServ->get_token();
    }

    public function test_is_valid_token_ok()
    {
        $post=["user"=>"fulanito","password"=>"menganitox"];
        $oServ = new LoginService("localhost:300",$post);
        $token = $oServ->get_token();
        $oServ = new LoginService("localhost:300");
        $isvalid = $oServ->is_valid($token);
        $this->assertTrue($isvalid);
    }


    public function test_valid_token_nok()
    {
        $post=["user"=>"fulanito","password"=>"menganitox"];
        $oServ = new LoginService("localhost:300",$post);
        $token = $oServ->get_token();
        $token .= "xxxx";

        $oServ = new LoginService("localhost:300");
        $oServ->is_valid($token);
    }

}//LoginServiceTest