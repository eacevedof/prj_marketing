<?php
// en: /<project>/backend 
// ./vendor/bin/phpunit --bootstrap ./vendor/theframework/bootstrap.php ./src/tests/Contexts/EncdecryptTest.php --color=auto
// ./vendor/bin/phpunit --bootstrap ./vendor/theframework/bootstrap.php ./src/tests
use PHPUnit\Framework\TestCase;
use Test\Traits\LogTrait;

use TheFramework\Components\Db\Context\ComponentContext;

final class ContextsTest extends TestCase
{
    use LogTrait;

    public function test_get_context()
    {
        $oComp = new ComponentContext();
        $arConfig = $oComp->get_config();
        print_r($arConfig);
        $this->assertEquals(TRUE,is_array($arConfig));
    }

    public function test_get_context_using_file()
    {
        $sPathfile = __DIR__ . DIRECTORY_SEPARATOR;
        print_r($sPathfile);
        $oComp = new ComponentContext($sPathfile);
        $arConfig = $oComp->get_config();
        print_r($arConfig);
        print_r($oComp->get_errors());
        $this->assertEquals(TRUE,is_array($arConfig));
    }
    
    public function test_get_by_id()
    {
        $oComp = new ComponentContext();
        $arConfig = $oComp->get_config();
        print_r($oComp->get_by_id("xxx"));
        $this->assertEquals(TRUE,is_array($arConfig));
    }

}//ContextsTest