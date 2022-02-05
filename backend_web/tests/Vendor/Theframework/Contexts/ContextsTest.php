<?php
namespace Tests\Vendor\Theframework\Contexts;

use Tests\Boot\AbsTestBase;
use TheFramework\Components\Db\Context\ComponentContext;

final class ContextsTest extends AbsTestBase
{
    public function test_get_context()
    {
        $oComp = new ComponentContext();
        $arConfig = $oComp->get_config();
        $this->log($arConfig,"contexts.test_get_context");
        $this->assertEquals(true, is_array($arConfig));
    }

    public function test_get_context_using_file()
    {
        $sPathfile = __DIR__ . DIRECTORY_SEPARATOR;
        $oComp = new ComponentContext($sPathfile);
        $arConfig = $oComp->get_config();
        $this->assertEquals(true, is_array($arConfig));
    }
    
    public function test_get_by_id()
    {
        $oComp = new ComponentContext();
        $arConfig = $oComp->get_config();
        $this->assertEquals(true, is_array($arConfig));
    }

}//ContextsTest