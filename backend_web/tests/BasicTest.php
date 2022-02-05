<?php
namespace Tests;

use Tests\Boot\AbsTestBase;

final class BasicTest extends AbsTestBase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testOk()
    {
        $this->assertSame(true, 1=="1");
    }
}