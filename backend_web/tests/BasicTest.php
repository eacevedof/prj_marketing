<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase
{
    public function testOk()
    {
        $this->assertSame(true, 1=="1");
    }
}