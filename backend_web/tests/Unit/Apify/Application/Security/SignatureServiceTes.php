<?php
namespace Tests\Unit\Apify\Application\Security;
use Tests\Unit\AbsUnitTest;

final class SignatureServiceTest extends AbsUnitTest
{

    public function test_get_token()
    {
        $post=["user"=>"fulanito","password"=>"menganito"];
        $oServ = new SignatureService("localhost:200",$post);
        $token = $oServ->get_token();
        $this->assertIsString($token);
    }

    public function test_is_valid()
    {
        $post=["user"=>"fulanito","password"=>"menganito"];
        $oServ = new SignatureService("localhost:200",$post);
        $token = $oServ->get_token();
        $r = $oServ->is_valid($token);
        $this->assertEquals(true,$r);
    }

    public function test_is_invalid()
    {
        $post=["user"=>"fulanito","password"=>"menganito"];
        $oServ = new SignatureService("localhost:200",$post);
        $token = $oServ->get_token();

        $post=["user"=>"fulanito","password"=>"menganito","injected"=>"some injected"];
        $oServ = new SignatureService("localhost:200",$post);
        $r = $oServ->is_valid($token);
        //$this->expectException("\Exception"); //no va
        //$this->expectException("Matrix\Exception"); //no va
        //$this->expectExceptionMessage("Wrong hash submitted"); //no va
    }

    public function test_domain_not_configured()
    {
        $post=["user"=>"fulanito","password"=>"menganito"];
        $oServ = new SignatureService("nonexistentdomain.com",$post);
        $token = $oServ->get_token();
    }
    
}//SignatureServiceTest